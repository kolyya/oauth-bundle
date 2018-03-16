<?php

namespace Kolyya\OAuthBundle\Security\Core\User;

use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class OAuthUserProvider
 * @package AppBundle\Security\Core\User
 */
class OAuthUserProvider extends BaseClass
{
    private $container;
    private $translator;
    private $mailerUser;
    private $service;
    private $socialId;
    private $token;
    public function  __construct(
        TranslatorInterface $translator,
        ContainerInterface $container,
        UserManagerInterface $userManager,
        $mailerUser = null
    )
    {
        $this->container = $container;
        $this->translator = $translator;
        $this->mailerUser = $mailerUser;

        $this->userManager = $userManager;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Срабатывает тогда, когда пользователь пытается войти через соц. сеть, хотя при этом еще не авторизован
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {

        // записываем AccessToken, может пригодится для запроса данных из некоторых соц. сетей
        $this->token = $response->getAccessToken();

        // записываем название соц. сети
        $this->service = $response->getResourceOwner()->getName();

        // записываем id соц. сети для поиска пользователя
        $this->socialId = $response->getUsername();

        // если $this->socialId не удалось получить, то пытаемся достать его из токена
        if(!$this->socialId){

            $rawToken = $response->getOAuthToken()->getRawToken();

            if(isset($rawToken['user_id']) && $rawToken['user_id'])
                $this->socialId = $rawToken['user_id'];
        }

        $user = null;

        // пытаемся найти пользователя по id
        if($this->socialId)
            $user = $this->userManager->findUserBy(array($this->service.'Id'=>$this->socialId));

//        var_dump([
//            'service' => $this->service,
//            'socialId' => $this->socialId,
//            'getNickname' => $response->getNickname(),
//            'getFirstName' => $response->getFirstName(),
//            'getLastName' => $response->getLastName(),
//            'getRealName' => $response->getRealName(),
//            'getEmail' => $response->getEmail(),
//            'getProfilePicture' => $response->getProfilePicture(),
//        ]);
//        exit();

        // если пользователь не найден, создает нового
        if(null === $user){

            // пытаемся найти пользователя по email
            $user = $this->userManager->findUserByEmail($response->getEmail());

            // если пользователя с такой почтой не найдено
            if (null === $user || !$user instanceof UserInterface) {

                $user = $this->userManager->createUser();
                if($response->getEmail()) {
                    $user->setEmail($response->getEmail());
                } else {
                    $user->setEmail('');
                    // todo: что делать, если нет почты у соц. сети?
                }
                $user->setPlainPassword(md5(uniqid()));
                $user->setEnabled(true);

            }

            // обрабатываем соц. сети
            switch ($this->service){
                case 'vkontakte':
                    $user->setVkontakteId($this->socialId);
                    $user->setUsername('vk_'.$this->socialId);
                    break;
                case 'facebook':
                    $user->setFacebookId($this->socialId);
                    $user->setUsername('fb_'.$this->socialId);
                    break;
                case 'odnoklassniki':
                    $user->setOdnoklassnikiId($this->socialId);
                    $user->setUsername('ok_'.$this->socialId);
                    break;
                case 'mailru':
                    $user->setMailruId($this->socialId);
                    $user->setUsername('mr_'.$this->socialId);
                    break;
                case 'google':
                    $user->setGoogleId($this->socialId);
                    $user->setUsername('gg_'.$this->socialId);
                    break;
            }

            // запрашиваем данные для сохранения
            $user = $this->setData($user, $response);

            // обновляем пользователя
            $this->userManager->updateUser($user);

            // отсылаем пользователю сообщение о регистрации
            //$this->sendOAuthEmail($user);

        }

        // если пользователь найден
        else {
            $checker = new UserChecker();
            $checker->checkPreAuth($user);
        }

        return $user;
    }

    /**
     * Срабатывает тогда, когда авторизованный пользователь пытается подключить еще одну соц.сеть
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Expected an instance of FOS\UserBundle\Model\User, but got "%s".', get_class($user)));
        }

        // записываем AccessToken, может пригодится для запроса данных из некоторых соц. сетей
        $this->token = $response->getAccessToken();

        // записываем название соц. сети
        $this->service = $response->getResourceOwner()->getName();

        // записываем id соц. сети для поиска пользователя
        $this->socialId = $response->getUsername();

        $property = $this->getProperty($response);

        // Symfony <2.5 BC
        if (method_exists($this->accessor, 'isWritable') && !$this->accessor->isWritable($user, $property)
            || !method_exists($this->accessor, 'isWritable') && !method_exists($user, 'set'.ucfirst($property))) {
            throw new \RuntimeException(sprintf("Class '%s' must have defined setter method for property: '%s'.", get_class($user), $property));
        }

        // Пытаемся найти по этой соц. сети и id
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $this->socialId))) {
            $this->disconnect($previousUser, $response);
        }

        // записываем данные в бд
        $user = $this->setData($user, $response);

        $this->accessor->setValue($user, $property, $this->socialId);

        // обновляем пользователя
        $this->userManager->updateUser($user);

        // отсылаем ему email
        //$this->sendConnectEmail($user);
    }

    public function getProperty(UserResponseInterface $response)
    {
        return $response->getResourceOwner()->getName().'Id';
    }

    private function sendOAuthEmail($user){
        $mailer = $this->container->get('mailer');
        $templating = $this->container->get('templating');
        $message = \Swift_Message::newInstance()
            ->setSubject($this->translator->trans('email.welcome_title'))
            ->setFrom($this->mailerUser,$this->translator->trans('email.welcome_user'))
            ->setTo($user->getEmail())
            ->setBody(
                $templating->render('oauth/email_to_user.html.twig',array(
                    'service' => $this->service,
                    'id' => $this->socialID
                )),'text/html'
            )
        ;
        $mailer->send($message);
        return true;
    }

    private function sendConnectEmail($user){
        $mailer = $this->container->get('mailer');
        $templating = $this->container->get('templating');
        $message = \Swift_Message::newInstance()
            ->setSubject($this->translator->trans('email.connect_title'))
            ->setFrom($this->mailerUser,$this->translator->trans('email.connect_user'))
            ->setTo($user->getEmail())
            ->setBody(
                $templating->render(':user:email_connect.html.twig',array(
                        'service' => $this->service,
                        'id' => $this->socialID
                    )),'text/html'
            )
        ;
        $mailer->send($message);
        return true;
    }

    // запрашивает данные для записи в бд
    private function setData($user, UserResponseInterface $response){
        $data = array(
            'photo_small'       => null,
            'photo_medium'      => null,
            'photo_big'         => null,
            'nickname'          => null,
            'gender'            => null,
            'first_name'        => null,
            'last_name'         => null,
            'birthday'          => null,
            'email'             => null,
            'mobile_phone'      => null,
        );
        $contactName = 'Пользователь';
        switch ($this->service){
            case 'vkontakte':
                $fields = array('photo_50', 'photo_200_orig', 'photo_max_orig', 'sex','bdate','nickname','contacts');
                $url = 'https://api.vk.com/method/users.get?user_ids='.$this->socialId.'&v=5.71&fields='.implode(',',$fields);
                $json = file_get_contents($url);
                $obj = json_decode($json)->response[0];

                $data['gender'] = array(1 => 'female', 2 => 'male', 0 => null)[$obj->sex];
                $data['first_name'] = $obj->first_name;
                $data['last_name'] = $obj->last_name;
                $data['birthday'] = isset($obj->bdate) ? $obj->bdate : null;
                $data['photo_small'] = isset($obj->photo_50) ? $obj->photo_50 : null;
                $data['photo_medium'] = isset($obj->photo_200_orig) ? $obj->photo_200_orig : null;
                $data['photo_big'] = isset($obj->photo_max_orig) ? $obj->photo_max_orig : null;
                $data['nickname'] = strlen($obj->nickname) ? $obj->nickname : null;
                $data['email'] = $response->getEmail();
                $data['mobile_phone'] = isset($obj->contacts) && isset($obj->contacts->mobile_phone) ? $obj->contacts->mobile_phone : null;

                if(!$user->getAvatarId()) $user->setAvatarId('vk');
                $user->setVkontakteData($data);
                break;
            case 'facebook':
                $fields = array('first_name','last_name','birthday','gender','picture','middle_name','email');
                $graph = 'https://graph.facebook.com/v2.0/'.$this->socialId.'?access_token='.$this->token;
                $url = $graph.'&fields='.implode(',',$fields);
                $obj = json_decode(file_get_contents($url));

                $data['gender'] = isset($obj->gender) ? $obj->gender : null;
                $data['first_name'] = $obj->first_name;
                $data['last_name'] = $obj->last_name;
                $data['birthday'] = isset($obj->birthday) ? date('d.m.Y',strtotime($obj->birthday)) : null;
                $data['photo_small'] = $obj->picture->data->url;
                $data['nickname'] = isset($obj->middle_name) ? $obj->middle_name : null;
                $data['email'] = $response->getEmail();

                $url = $graph.'&fields=picture.type(large)';
                $obj = json_decode(file_get_contents($url));
                $data['photo_medium'] = $obj->picture->data->url;


                $url = $graph.'&fields=picture.width(400)';
                $obj = json_decode(file_get_contents($url));
                $data['photo_big'] = $obj->picture->data->url;

                if(!$user->getAvatarId()) $user->setAvatarId('fb');
                $user->setFacebookData($data);
                break;
            case 'google':
                $params = array(
                    'access_token' => $this->token,
                    'key' => $this->container->getParameter('oauth.google.client_secret'),
                );
                $json = file_get_contents('https://www.googleapis.com/plus/v1/people/'.$this->socialId.'?'.http_build_query($params));
                $obj = json_decode($json);

                $data['gender'] = isset($obj->gender) ? $obj->gender : null;
                $data['first_name'] = $obj->name->givenName;
                $data['last_name'] = $obj->name->familyName;
                if(isset($obj->image) && isset($obj->image->url)){
                    $data['photo_small'] =  $obj->image->url;
                    $data['photo_medium'] = substr($obj->image->url,0,-2).'200';
                    $data['photo_big'] = substr($obj->image->url,0,-2).'400';
                }

                $data['nickname'] = isset($obj->nickname) ? $obj->nickname : null;
                $data['email'] = $response->getEmail();

                if(!$user->getAvatarId()) $user->setAvatarId('gg');
                $user->setGoogleData($data);
                break;
            case 'odnoklassniki':
                $fields = array('GENDER', 'FIRST_NAME', 'LAST_NAME','NAME','HAS_EMAIL','BIRTHDAY', 'EMAIL', 'PIC50X50','PIC190X190','PIC600X600');
                $params = array(
                    'application_key' => $this->container->getParameter('oauth.odnoklassniki.application_key'),
                    'emptyPictures' => true,
                    'fields' => implode(',',$fields),
//                    'format' => 'json',
                    'method' => 'users.getInfo',
                    'uids' => $this->socialId,
                    'access_token' => $this->token
                );

                ksort($params);
                $string = '';
                foreach($params as $key => $val){
                    if($key == 'access_token') continue;
                    $string .= $key.'='.$val;
                }
                $params['sig'] = md5($string.md5($this->token.$this->container->getParameter('oauth.odnoklassniki.client_secret')));
                $url = 'https://api.ok.ru/fb.do?'.http_build_query($params);
                $json = file_get_contents($url);
                $obj = json_decode($json)[0];

                $data['gender'] = isset($obj->gender) ? $obj->gender : null;
                $data['first_name'] = strlen($obj->first_name) ? $obj->first_name : '-';
                $data['last_name'] = strlen($obj->last_name) ? $obj->last_name : '-';
                $data['birthday'] = isset($obj->birthday) ? date('d.m.Y',strtotime($obj->birthday)) : null;
                $data['photo_small'] = isset($obj->pic50x50) ? $obj->pic50x50 : null;
                $data['photo_medium'] = isset($obj->pic190x190) ? $obj->pic190x190 : null;
                $data['photo_big'] = isset($obj->pic600x600) ? $obj->pic600x600 : null;
                $data['email'] = $response->getEmail();

                if(!$user->getAvatarId()) $user->setAvatarId('ok');
                $user->setOdnoklassnikiData($data);
                break;
            case 'mailru':
                $params = array(
                    'app_id' => $this->container->getParameter('oauth.mailru.client_id'),
                    'method' => 'users.getInfo',
                    'uids' => $this->socialId,
                    'secure' => 1,
                    'session_key' => $this->token
                );
                ksort($params);
                $string = '';
                foreach($params as $key => $val){
                    $string .= $key.'='.$val;
                }
                $params['sig'] = md5($string.$this->container->getParameter('oauth.mailru.client_secret'));
                $url = 'http://www.appsmail.ru/platform/api?'.http_build_query($params);
                $json = file_get_contents($url);
                $obj = json_decode($json)[0];

                $data['gender'] = array(1 => 'female', 0 => 'male')[$obj->sex];
                $data['first_name'] = $obj->first_name;
                $data['last_name'] = $obj->last_name;
                $data['birthday'] = $obj->birthday;
                if($obj->has_pic){
                    $data['photo_small'] = isset($obj->pic_50) ? $obj->pic_50 : null;
                    $data['photo_medium'] = isset($obj->pic_190) ? $obj->pic_190 : null;
                    $data['photo_big'] = isset($obj->pic_big) ? $obj->pic_big : null;
                }
                $data['nickname'] = isset($obj->nick) ? $obj->nick : null;
                $data['email'] = $response->getEmail();

                if(!$user->getAvatarId()) $user->setAvatarId('mr');
                $user->setMailruData($data);
                break;
        }

        if($data['first_name'] || $data['last_name']) $contactName = $data['first_name'].($data['first_name'] ? ' ' : '').$data['last_name'];
//        $user->setContactName($contactName);

        return $user;
    }

}
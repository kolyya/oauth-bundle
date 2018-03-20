<?php

namespace Kolyya\OAuthBundle\Security\Core\User;

use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Kolyya\OAuthBundle\Entity\OAuthUser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class OAuthUserProvider
 */
class OAuthUserProvider extends BaseClass
{
    private $container;
    private $translator;

    // тут хранится весь ресурс
    /**
     * @var $resourceOwner \HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface
     */
    private $resourceOwner;

    private $service;
    private $socialId; // 1232131 838483483
    private $token;

    /**
     * OAuthUserProvider constructor.
     * @param TranslatorInterface $translator
     * @param ContainerInterface $container
     * @param UserManagerInterface $userManager
     */
    public function  __construct(
        TranslatorInterface $translator,
        ContainerInterface $container,
        UserManagerInterface $userManager
        //,$mailerUser = null
    )
    {
        $this->container = $container;
        $this->translator = $translator;
        //$this->mailerUser = $mailerUser;

        $this->userManager = $userManager;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Срабатывает тогда, когда пользователь пытается войти через соц. сеть, хотя при этом еще не авторизован
     * @param UserResponseInterface $response
     * @return \FOS\UserBundle\Model\UserInterface|null
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $this->__processResponse($response);
        /**
         * @var $user \App\Entity\User
         */
        $user = null;

        // пытаемся найти пользователя по id
        $user = $this->getUserBySoc($response);

        if(null === $user){

            // если пользователь НЕ найден
            // создает нового c новым паролем и автивированного
            // запрашиваем данные для сохранения
            // ставим id соц. сети
            // ставим новый УНИКАЛЬНЫЙ юзернейм

            $user = $this->userManager->createUser();
            $user->setEmail('');
            $user->setPlainPassword(md5(uniqid()));
            $user->setEnabled(true);

            $user = $this->setData($user, $response);
            $user = $this->setUsername($user, $response);

            $user->{'set'.ucfirst($this->service).'Id'}($this->socialId);

            // обновляем пользователя
            $this->userManager->updateUser($user);
        } else {

            // если пользователь найден

            $checker = new UserChecker();
            $checker->checkPreAuth($user);
        }

        return $user;
    }

    /**
     * Срабатывает тогда, когда авторизованный пользователь пытается подключить еще одну соц.сеть
     * @param UserInterface $user
     * @param UserResponseInterface $response
     * @throws \TypeError
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $this->__processResponse($response);

        // пытаемся найти пользователя по id
        // если найден, то удаляем его данные из предыдущего аккаунта
        if($previousUser = $this->getUserBySoc($response)){
            $previousUser->{'set'.ucfirst($this->service).'Id'}(null);
            $previousUser->{'set'.ucfirst($this->service).'Data'}(null);

            $this->userManager->updateUser($previousUser);
        }

        $user->{'set'.ucfirst($this->service).'Id'}($this->socialId);
        $this->setData($user, $response);

        // обновляем пользователя
        $this->userManager->updateUser($user);
    }

    public function getProperty(UserResponseInterface $response)
    {
        return $response->getResourceOwner()->getName().'Id';
    }

    /**
     * Данные из запроса записывает в параметры
     * @param UserResponseInterface $response
     */
    private function __processResponse(UserResponseInterface $response)
    {
        // записываем AccessToken, может пригодится для запроса данных из некоторых соц. сетей
        $this->token = $response->getAccessToken();

        // записываем ResourceOwner
        $this->resourceOwner = $response->getResourceOwner();

        // записываем название соц. сети
        $this->service = $this->resourceOwner->getName();

        // записываем id соц. сети для поиска пользователя
        $this->socialId = $response->getUsername();

        // если $this->socialId не удалось получить, то пытаемся достать его из токена
        if(!$this->socialId){

            $rawToken = $response->getOAuthToken()->getRawToken();

            if(isset($rawToken['user_id']) && $rawToken['user_id'])
                $this->socialId = $rawToken['user_id'];
        }
    }

    /**
     * запрашивает данные для записи в бд
     * @param $user \App\Entity\User|UserInterface
     * @param UserResponseInterface $response
     * @return mixed
     */
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
        //$contactName = 'Пользователь';
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

                $user->setFacebookData($data);
                break;
            case 'google':
                $params = array(
                    'access_token' => $this->token,
                    'key' => $this->resourceOwner->getOption('client_secret'),
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

                $user->setGoogleData($data);
                break;
            case 'odnoklassniki':
                $fields = array('GENDER', 'FIRST_NAME', 'LAST_NAME','NAME','HAS_EMAIL','BIRTHDAY', 'EMAIL', 'PIC50X50','PIC190X190','PIC600X600');
                $params = array(
                    'application_key' => $this->resourceOwner->getOption('application_key'),
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
                $params['sig'] = md5($string.md5($this->resourceOwner->getOption('client_secret')));
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

                $user->setOdnoklassnikiData($data);
                break;
            case 'mailru':
                $params = array(
                    'app_id' => $this->resourceOwner->getOption('client_id'),
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
                $params['sig'] = md5( $this->resourceOwner->getOption('client_secret') );
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

                $user->setMailruData($data);
                break;
        }

        return $user;
    }

    /**
     * @param $user \App\Entity\User|UserInterface
     * @param UserResponseInterface $response
     * @return \App\Entity\User|UserInterface
     */
    private function setUsername($user, UserResponseInterface $response)
    {
        switch ($this->service)
        {
            // todo: в зависимости от соц. сети ставить нормальный username
            default:
                $username = OAuthUser::$IDS[$this->service].'_'.$this->socialId;
        }

        $i = 0;
        while (!$user->getUsername())
        {
            // если пользователь с таким username уже существует
            $testUsername = $username.($i?'_'.$i:'');
            if($this->userManager->findUserBy(array('username' => $testUsername))) {
                $i++;
                continue;
            }

            $user->setUsername($testUsername);
        }

        return $user;
    }

    private function getUserBySoc(UserResponseInterface $response)
    {
        $user = null;
        if($this->socialId)
            $user = $this->userManager
                ->findUserBy(array(
                    $this->getProperty($response) => $this->socialId
                ));

        return $user;
    }
}
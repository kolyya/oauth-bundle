<?php

namespace Kolyya\OAuthBundle\Templating\Helper;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\Helper\Helper;

class OAuthHelper extends Helper
{
    public static $IDS = array(
        'vkontakte'         => 'vk',
        'facebook'          => 'fb',
        'odnoklassniki'     => 'ok',
        'mailru'            => 'mr',
        'google'            => 'gg',
    );

    /**
     * @var $user \App\Entity\User
     */
    private $user;
    private $templating;
    private $config;

    public function __construct(TokenStorageInterface $tokenStorage, $templating, $config)
    {
        if($tokenStorage->getToken())
            $this->user = $tokenStorage->getToken()->getUser();
        $this->templating = $templating;
        $this->config = $config;
    }

    public function getOauthButtons(){

        return $this->templating->render('KolyyaOAuthBundle:OAuth:auth.html.twig', array(
            'order' => $this->config['order'],
            'ids' => self::$IDS
        ));
    }

    public function getConnectButtons(){

        $buttons = array();

        foreach ($this->config['order'] as $item){
            array_push($buttons, array(
               'item' => $item,
               'item_id' => self::$IDS[$item],
               'soc_id' => $this->user->{'get'.ucfirst($item).'Id'}(),
               'soc_data' => $this->user->{'get'.ucfirst($item).'Data'}(),
            ));
        }

        return $this->templating->render('KolyyaOAuthBundle:OAuth:connect.html.twig', array(
            'buttons' => $buttons,
        ));
    }

    /**
     * Returns the name of the helper.
     *
     * @return string The helper name
     */
    public function getName()
    {
        return 'kolyya_oauth';
    }
}

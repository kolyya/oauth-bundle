<?php

namespace Kolyya\OAuthBundle\Templating\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
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

    private $em;
    private $container;
    private $templating;
    private $config;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container, $templating, $config)
    {
        $this->em = $em;
        $this->container = $container;
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

        return $this->templating->render('KolyyaOAuthBundle:OAuth:connect.html.twig', array(
            'order' => $this->config['order'],
            'ids' => self::$IDS
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

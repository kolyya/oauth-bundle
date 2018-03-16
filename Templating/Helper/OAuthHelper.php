<?php

namespace Kolyya\OAuthBundle\Templating\Helper;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Templating\Helper\Helper;

class OAuthHelper extends Helper
{

    private $em;
    private $container;
    private $templating;
    private $order;

    public function __construct(EntityManager $em, Container $container, $templating, $order)
    {
        $this->em = $em;
        $this->container = $container;
        $this->templating = $templating;
        $this->order = $order;
    }

    public function getButtons(){

        $ids = array(
            'vkontakte' => 'vk',
            'facebook' => 'fb',
            'odnoklassniki' => 'ok',
            'mailru' => 'mr',
            'google' => 'gg',
        );

        return $this->templating->render('KolyyaOAuthBundle:OAuth:soc_auth.html.twig',array(
            'order' => $this->order,
            'ids' => $ids
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

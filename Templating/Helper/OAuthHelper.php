<?php

namespace Kolyya\OAuthBundle\Templating\Helper;

use Kolyya\OAuthBundle\Enum\ServiceIdEnum;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\Helper\Helper;

class OAuthHelper extends Helper
{
    /**
     * @var $user \App\Entity\User
     */
    private $user;

    /**
     * @var \Symfony\Bundle\TwigBundle\TwigEngine
     */
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
            'ids' => ServiceIdEnum::$IDS
        ));
    }

    public function getConnectButtons(){

        $buttons = array();

        foreach ($this->config['order'] as $item){
            array_push($buttons, array(
               'item' => $item,
               'item_id' => ServiceIdEnum::$IDS[$item],
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

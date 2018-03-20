<?php

namespace Kolyya\OAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

abstract class OAuthUser extends BaseUser
{
    public static $IDS = array(
        'vkontakte'         => 'vk',
        'facebook'          => 'fb',
        'odnoklassniki'     => 'ok',
        'mailru'            => 'mr',
        'google'            => 'gg',
    );

    private $oAuthToken;

    /**
     * @return mixed
     */
    public function getOAuthToken()
    {
        return $this->oAuthToken;
    }

    /**
     * @param mixed $oAuthToken
     */
    public function setOAuthToken($oAuthToken)
    {
        $this->oAuthToken = $oAuthToken;
    }
}
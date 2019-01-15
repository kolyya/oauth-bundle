<?php

namespace Kolyya\OAuthBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Kolyya\OAuthBundle\Entity\Traits\OAuthTokenTrait;

abstract class OAuthUser extends BaseUser
{
    use OAuthTokenTrait;

    public static $IDS = array(
        'vkontakte'         => 'vk',
        'facebook'          => 'fb',
        'odnoklassniki'     => 'ok',
        'mailru'            => 'mr',
        'google'            => 'gg',
    );
}
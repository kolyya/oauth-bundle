<?php

namespace Kolyya\OAuthBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Kolyya\OAuthBundle\Entity\Traits\OAuthTokenTrait;

/**
 * @package Kolyya\OAuthBundle\Entity
 * @deprecated
 */
abstract class OAuthUser extends BaseUser
{
    use OAuthTokenTrait;
}
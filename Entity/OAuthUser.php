<?php

namespace Kolyya\OAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

abstract class OAuthUser extends BaseUser
{
    private $oAuthToken;

    /**
     * @ORM\Column(type="string",length=32, nullable=true)
     */
    private $avatarId;

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

    /**
     * @return mixed
     */
    public function getAvatarId()
    {
        return $this->avatarId;
    }

    /**
     * @param mixed $avatarId
     */
    public function setAvatarId($avatarId): void
    {
        $this->avatarId = $avatarId;
    }
}
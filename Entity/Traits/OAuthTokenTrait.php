<?php

namespace Kolyya\OAuthBundle\Entity\Traits;

trait OAuthTokenTrait
{
    protected $oAuthToken;

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
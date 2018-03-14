<?php

namespace Kolyya\OAuthBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

abstract class OAuthUser extends BaseUser
{
    private $oAuthToken;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $vkontakteData;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $vkontakteId;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $facebookData;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $facebookId;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $odnoklassnikiData;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $odnoklassnikiId;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $mailruData;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $mailruId;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $googleData;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $googleId;

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
    public function getVkontakteData()
    {
        return $this->vkontakteData;
    }

    /**
     * @param mixed $vkontakteData
     */
    public function setVkontakteData($vkontakteData)
    {
        $this->vkontakteData = $vkontakteData;
    }

    /**
     * @return mixed
     */
    public function getFacebookData()
    {
        return $this->facebookData;
    }

    /**
     * @param mixed $facebookData
     */
    public function setFacebookData($facebookData)
    {
        $this->facebookData = $facebookData;
    }

    /**
     * @return mixed
     */
    public function getOdnoklassnikiData()
    {
        return $this->odnoklassnikiData;
    }

    /**
     * @param mixed $odnoklassnikiData
     */
    public function setOdnoklassnikiData($odnoklassnikiData)
    {
        $this->odnoklassnikiData = $odnoklassnikiData;
    }

    /**
     * @return mixed
     */
    public function getMailruData()
    {
        return $this->mailruData;
    }

    /**
     * @param mixed $mailruData
     */
    public function setMailruData($mailruData)
    {
        $this->mailruData = $mailruData;
    }

    /**
     * @return mixed
     */
    public function getGoogleData()
    {
        return $this->googleData;
    }

    /**
     * @param mixed $googleData
     */
    public function setGoogleData($googleData)
    {
        $this->googleData = $googleData;
    }

    /**
     * @return mixed
     */
    public function getVkontakteId()
    {
        return $this->vkontakteId;
    }

    /**
     * @param mixed $vkontakteId
     */
    public function setVkontakteId($vkontakteId)
    {
        $this->vkontakteId = $vkontakteId;
    }

    /**
     * @return mixed
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param mixed $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    /**
     * @return mixed
     */
    public function getOdnoklassnikiId()
    {
        return $this->odnoklassnikiId;
    }

    /**
     * @param mixed $odnoklassnikiId
     */
    public function setOdnoklassnikiId($odnoklassnikiId)
    {
        $this->odnoklassnikiId = $odnoklassnikiId;
    }

    /**
     * @return mixed
     */
    public function getMailruId()
    {
        return $this->mailruId;
    }

    /**
     * @param mixed $mailruId
     */
    public function setMailruId($mailruId)
    {
        $this->mailruId = $mailruId;
    }

    /**
     * @return mixed
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * @param mixed $googleId
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
    }
}
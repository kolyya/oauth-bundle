<?php

namespace Kolyya\OAuthBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait GoogleTrait
{
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
    public function getGoogleData()
    {
        return $this->googleData;
    }

    /**
     * @param mixed $googleData
     */
    public function setGoogleData($googleData): void
    {
        $this->googleData = $googleData;
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
    public function setGoogleId($googleId): void
    {
        $this->googleId = $googleId;
    }
}
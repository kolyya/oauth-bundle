<?php

namespace Kolyya\OAuthBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait FacebookTrait
{
    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $facebookData;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $facebookId;

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
    public function setFacebookData($facebookData): void
    {
        $this->facebookData = $facebookData;
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
    public function setFacebookId($facebookId): void
    {
        $this->facebookId = $facebookId;
    }
}
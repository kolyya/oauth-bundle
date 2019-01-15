<?php

namespace Kolyya\OAuthBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait VkontakteTrait
{
    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $vkontakteData;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $vkontakteId;

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
    public function setVkontakteData($vkontakteData): void
    {
        $this->vkontakteData = $vkontakteData;
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
    public function setVkontakteId($vkontakteId): void
    {
        $this->vkontakteId = $vkontakteId;
    }
}
<?php

namespace Kolyya\OAuthBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait OdnoklassnikiTrait
{
    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $odnoklassnikiData;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $odnoklassnikiId;

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
    public function setOdnoklassnikiData($odnoklassnikiData): void
    {
        $this->odnoklassnikiData = $odnoklassnikiData;
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
    public function setOdnoklassnikiId($odnoklassnikiId): void
    {
        $this->odnoklassnikiId = $odnoklassnikiId;
    }
}
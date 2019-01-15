<?php

namespace Kolyya\OAuthBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait MailruTrait
{
    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $mailruData;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $mailruId;

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
    public function setMailruData($mailruData): void
    {
        $this->mailruData = $mailruData;
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
    public function setMailruId($mailruId): void
    {
        $this->mailruId = $mailruId;
    }
}
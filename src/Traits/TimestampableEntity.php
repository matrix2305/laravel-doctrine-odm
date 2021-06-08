<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Traits;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;


trait TimestampableEntity
{
    /**
     * @var ?\DateTime
     * @ODM\Field(type="date")
     */
    private ?\DateTime $createdAt = null;

    /**
     * @var \DateTime
     * @ODM\Field(type="date")
     */
    private \DateTime $updatedAt;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt) : void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setTimestamps() : void
    {
        if ($this->createdAt !== null){
            $this->createdAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }
}
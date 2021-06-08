<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Traits;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;


trait TimestampableEntity
{
    /**
     * @var ?\DateTime
     * @ODM\Field(type="date", nullable=true)
     */
    private ?\DateTime $createdAt;

    /**
     * @var \DateTime
     * @ODM\Field(type="date")
     */
    private \DateTime $updatedAt;

    public function __call($method, $args) : void
    {
        if (str_contains(strtolower($method), 'set')){
            if ($this->createdAt === null){
                $this->createdAt = new \DateTime();
            }
            $this->updatedAt = new \DateTime();
        }
    }

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
}
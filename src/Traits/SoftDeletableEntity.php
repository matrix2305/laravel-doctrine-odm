<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Traits;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

trait SoftDeletableEntity
{
    /**
     * @ODM\Field(type="date", nulllable=true)
     * @ODM\Index
     */
    private ?\DateTime $deletedAt;

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }
}
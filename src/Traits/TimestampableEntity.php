<?php
declare(strict_types=1);
namespace LaravelDoctrineODM\Traits;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;

trait TimestampableEntity
{
    /**
     * @var \DateTime
     * @PHPCR\Field(type="date", property="jcr:created")
     */
    private \DateTime $created;

    /**
     * @var \DateTime
     * @PHPCR\Field(type="date", property="jcr:lastModified")
     */
    private \DateTime $lastModified;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->lastModified;
    }
}
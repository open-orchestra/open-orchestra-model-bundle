<?php

namespace OpenOrchestra\ModelBundle\MongoTrait;

use OpenOrchestra\ModelBundle\Document\EmbedStatus;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Trait Statusable
 */
trait Statusable
{
    /**
     * @var StatusInterface $status
     *
     * @ODM\EmbedOne(targetDocument="EmbedStatus")
     */
    protected $status;

    /**
     * Set status
     *
     * @param StatusInterface|null $status
     */
    public function setStatus(StatusInterface $status = null)
    {
        $this->status = null;
        if ($status instanceof StatusInterface) {
            $this->status = EmbedStatus::createFromStatus($status);
        }
    }

    /**
     * Get status
     *
     * @return StatusInterface $status
     */
    public function getStatus()
    {
        return $this->status;
    }
}

<?php

namespace OpenOrchestra\ModelBundle\MongoTrait;

use OpenOrchestra\ModelBundle\Document\EmbedStatus;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Trait Statusable
 *
 * @deprecated Use Statusable from ModelInterface, will be removed in 0.2.2
 */
trait Statusable
{
    /**
     * @var StatusInterface $status
     *
     * @ODM\EmbedOne(targetDocument="OpenOrchestra\ModelInterface\Model\EmbedStatusInterface")
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

<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\EmbedStatusInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Class EmbedStatus
 *
 * @ODM\EmbeddedDocument
 */
class EmbedStatus extends AbstractStatus implements EmbedStatusInterface
{
    /**
     * @param StatusInterface $status
     */
    public function __construct(StatusInterface $status)
    {
        parent::__construct();
        $this->id = $status->getId();
        $this->setName($status->getName());
        $this->setPublished($status->isPublished());
        $this->setInitial($status->isInitial());
        $this->setDisplayColor($status->getDisplayColor());
        $this->setLabels($status->getLabels());

        foreach ($status->getToRoles() as $toRole) {
            $this->addToRole($toRole);
        }

        foreach ($status->getFromRoles() as $fromRole) {
            $this->addFromRole($fromRole);
        }
    }

    /**
     * @param StatusInterface $status
     *
     * @return EmbedStatus
     */
    public static function createFromStatus(StatusInterface $status)
    {
        return new EmbedStatus($status);
    }
}

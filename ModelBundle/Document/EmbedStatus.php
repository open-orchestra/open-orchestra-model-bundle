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
        $this->setPublishedState($status->isPublishedState());
        $this->setInitialState($status->isInitialState());
        $this->setDisplayColor($status->getDisplayColor());
        $this->setLabels($status->getLabels());
        $this->setBlockedEdition($status->isBlockedEdition());
        $this->setOutOfWorkflow($status->isOutOfWorkflow());
        $this->setTranslationState($status->isTranslationState());
        $this->setAutoUnpublishToState($status->isAutoUnpublishToState());
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

<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use OpenOrchestra\ModelBundle\Document\TrashItem;
use OpenOrchestra\ModelInterface\Model\TrashCanDisplayableInterface;

/**
 * Class TrashCanListener
 */
class TrashCanListener
{
    public $entities = array();
    protected $trashItemClass;

    /**
     * @param string $trashItemClass
     */
    public function __construct($trashItemClass)
    {
        $this->trashItemClass = $trashItemClass;
    }

    /**
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PreUpdateEventArgs $event)
    {
        $document = $event->getDocument();
        if ($document instanceof TrashCanDisplayableInterface) {
            if ($event->hasChangedField('deleted') && $event->getNewValue('deleted') === true && $event->getOldValue('deleted') === false) {
                /**
                 * @var TrashItem $trashItemClass
                 */
                $trashCan = new $this->trashItemClass();
                $trashCan->setEntity($document);
                $trashCan->setName($document->getTrashCanName());
                $this->entities[] = $trashCan;

            }
        }
    }

    /**
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        if (!empty($this->entities)) {
            $documentManager = $event->getDocumentManager();
            foreach ($this->entities as $entity) {
                $documentManager->persist($entity);
                $documentManager->flush($entity);
            }
            $this->entities = array();
        }
    }
}

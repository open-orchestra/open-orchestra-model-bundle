<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use OpenOrchestra\ModelBundle\Document\TrashCan;
use OpenOrchestra\ModelInterface\Model\TrashCanableInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use DateTime;

/**
 * Class TrashCanListener
 */
class TrashCanListener extends ContainerAware
{
    public $entities = array();

    /**
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PreUpdateEventArgs $event)
    {
        $document = $event->getDocument();
        if ($document instanceof TrashCanableInterface) {
            if ($event->hasChangedField('deleted') && $event->getNewValue('deleted') === true && $event->getOldValue('deleted') === false) {
                $trashCanClass = $this->container->getParameter('open_orchestra_model.document.trash_can.class');
                /**
                 * @var TrashCan $trashCan
                 */
                $trashCan = new $trashCanClass();
                $trashCan->setEntity($document);
                $trashCan->setName($document->getTrashCanName());
                $date = new DateTime();
                $trashCan->setDeleteAt($date->format('Y-m-d H:i:s'));
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
            }
            $this->entities = array();
            $documentManager->flush();
        }
    }
}

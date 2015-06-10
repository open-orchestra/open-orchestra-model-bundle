<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class InitialStatusListener
 */
class InitialStatusListener extends ContainerAware
{
    protected $statuses = array();

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if ($document instanceof StatusInterface && $document->isPublished() && $document->isInitial()) {
            $statuses = $this->container->get('open_orchestra_model.repository.status')->findOtherByInitial($document->getName());
            foreach ($statuses as $status) {
                $status->setInitial(false);
                $this->statuses[] = $status;
            }
        }
    }

    /**
     * @param PostFlushEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        if (! empty($this->statuses)) {
            $documentManager = $eventArgs->getDocumentManager();
            foreach ($this->statuses as $status) {
                $documentManager->persist($status);
            }
            $this->statuses = array();
            $documentManager->flush();
        }
    }
}

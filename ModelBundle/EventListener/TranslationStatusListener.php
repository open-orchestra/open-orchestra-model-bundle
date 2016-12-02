<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class TranslationStatusListener
 */
class TranslationStatusListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $statuses = array();

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if ($document instanceof StatusInterface && $document->isTranslationState()) {
            $statuses = $this->container->get('open_orchestra_model.repository.status')
                ->findOtherByTranslationState($document->getName());
            foreach ($statuses as $status) {
                $status->setTranslationState(false);
                $this->statuses[] = $status;
            }
        }
    }

    /**
     * @param PostFlushEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        if (!empty($this->statuses)) {
            $documentManager = $eventArgs->getDocumentManager();
            foreach ($this->statuses as $status) {
                $documentManager->persist($status);
            }
            $this->statuses = array();
            $documentManager->flush();
        }
    }
}

<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use OpenOrchestra\ModelInterface\Model\IsStatusableInterface;

/**
 * Class SetInitialStatusListener
 */
class SetInitialStatusListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if ($document instanceof StatusableInterface && is_null($document->getStatus()) && (!$document instanceof IsStatusableInterface || $document->isStatusable())) {
            $status = $this->container->get('open_orchestra_model.repository.status')->findOneByInitial();
            if ($status instanceof StatusInterface) {
                $document->setStatus($status);
            }
        }
    }
}

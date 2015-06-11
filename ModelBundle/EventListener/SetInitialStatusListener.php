<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class SetInitialStatusListener
 */
class SetInitialStatusListener extends ContainerAware
{
    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if ($document instanceof StatusableInterface && is_null($document->getStatus())) {
            $status = $this->container->get('open_orchestra_model.repository.status')->findOneByInitial();
            if ($status instanceof StatusInterface) {
                $document->setStatus($status);
            }
        }
    }
}

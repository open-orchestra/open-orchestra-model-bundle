<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class SetInitialStatusListener
 */
class SetInitialStatusListener
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

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

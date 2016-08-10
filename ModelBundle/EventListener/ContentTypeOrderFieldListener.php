<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class ContentTypeOrderFieldListener
 */
class ContentTypeOrderFieldListener
{
    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $this->orderFields($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->orderFields($event);
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function orderFields(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if ($document instanceof ContentTypeInterface) {
            $fields = $document->getFields()->toArray();
            uasort($fields, function ($field1, $field2) {
                return $field1->getPosition() >= $field2->getPosition() ? 1 : -1;
            });
            $fields = array_values($fields);
            $document->setFields(new ArrayCollection($fields));
        }
    }
}

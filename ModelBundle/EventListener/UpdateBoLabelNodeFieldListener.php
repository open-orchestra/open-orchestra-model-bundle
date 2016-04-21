<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class UpdateBoLabelNodeFieldListener
 */
class UpdateBoLabelNodeFieldListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $nodeManaged = array();

    /**
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PreUpdateEventArgs $event)
    {
        if (!($nodeUpdate = $event->getDocument()) instanceof NodeInterface ||
            !$event->hasChangedField('boLabel') ||
            in_array($event->getDocument(), $this->nodeManaged)
        ) {
            return;
        }

        $boLabel = $nodeUpdate->getBoLabel();
        $this->nodeManaged[] = $nodeUpdate;
        /** @var NodeInterface $node */
        $nodes = $this->container->get('open_orchestra_model.repository.node')->findByNodeAndSite($nodeUpdate->getNodeId(), $nodeUpdate->getSiteId());
        foreach ($nodes as $node) {
            if (!in_array($node, $this->nodeManaged) && $boLabel !== $node->getBoLabel()) {
                $node->setBoLabel($boLabel);
                $this->nodeManaged[] = $node;
                $event->getDocumentManager()->flush($node);
            }
        }
    }
}

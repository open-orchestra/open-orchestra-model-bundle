<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use OpenOrchestra\ModelBundle\Manager\NodeManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelBundle\Repository\NodeRepository;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class TransversalNodeCreatorListener
 */
class TransversalNodeCreatorListener extends ContainerAware
{
    protected $nodeManager;

    public $nodes;

    /**
     * @param NodeManager $nodeManager
     */
    public function __construct(NodeManager $nodeManager)
    {
        $this->nodeManager = $nodeManager;
        $this->nodes = array();
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $this->checkNodes($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->checkNodes($event);
    }

    /**
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        if (!empty( $this->nodes)) {
            $documentManager = $event->getDocumentManager();
            foreach ($this->nodes as $node) {
                $documentManager->persist($node);
            }
            $this->nodes = array();
            $documentManager->flush();
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    protected function checkNodes(LifecycleEventArgs $event)
    {
        $document = $event->getDocument();

        if ($document instanceof SiteInterface) {
            foreach ($document->getLanguages() as $language) {
                $node = $this->getNodeRepository()
                    ->findInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, $language, $document->getSiteId());
                if (!$node instanceof NodeInterface) {
                    $this->nodes[] = $this->nodeManager->createTransverseNode($language, $document->getSiteId());
                }
            }
        }
    }

    /**
     * @return NodeRepository
     */
    protected function getNodeRepository()
    {
        return $this->container->get('open_orchestra_model.repository.node');
    }
}

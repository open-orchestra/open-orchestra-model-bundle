<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class GeneratePathListener
 */
class GeneratePathListener
{
    protected $container;
    protected $currentSiteManager;
    public $nodes;

    /**
     * @param Container $container
     * @param CurrentSiteIdInterface $currentSiteManager
     */
    public function __construct(Container $container, CurrentSiteIdInterface $currentSiteManager)
    {
        $this->container = $container;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $this->setPath($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->setPath($event);
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
     * @param LifecycleEventArgs $eventArgs
     */
    public function setPath(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if ($document instanceof NodeInterface) {
            $nodeRepository = $this->container->get('open_orchestra_model.repository.node');
            $nodeId = $document->getNodeId();
            $siteId = $document->getSiteId();
            $path = '';
            $parentNode = $nodeRepository->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(
                $document->getParentId(),
                $document->getLanguage(),
                $siteId
            );
            if ($parentNode instanceof NodeInterface) {
                $path = $parentNode->getPath() . '/';
            }
            $path .= $nodeId;
            if ($path != $document->getPath()) {
                $document->setPath($path);
                $this->nodes[] = $document;
                $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
                $childNodes = $nodeRepository->findChildsByPathAndSiteIdAndLanguage($document->getPath(), $siteId, $language);
                foreach($childNodes as $childNode){
                    $this->nodes[] = $childNode;
                    $childNode->setPath(preg_replace('/'.preg_quote($document->getPath(), '/').'(.*)/', $path.'$1', $childNode->getPath()));
                }
            }
        }
    }
}

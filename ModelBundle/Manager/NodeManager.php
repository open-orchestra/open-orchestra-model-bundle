<?php

namespace OpenOrchestra\ModelBundle\Manager;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Manager\NodeManagerInterface;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class NodeManager
 */
class NodeManager  extends ContainerAware implements NodeManagerInterface
{
    protected $nodeClass;

    /**
     * @param string $nodeClass
     * @param string $areaClass
     */
    public function __construct($nodeClass, $areaClass)
    {
        $this->nodeClass = $nodeClass;
        $this->areaClass = $areaClass;
    }

    /**
     * Create transverse node
     *
     * @param string $language
     * @param string $siteId
     *
     * @return NodeInterface
     */
    public function createTransverseNode($language, $siteId)
    {
        $area = new $this->areaClass();
        $area->setLabel('main');
        $area->setAreaId('main');

        /** @var NodeInterface $node */
        $node = new $this->nodeClass();
        $node->setLanguage($language);
        $node->setNodeId(NodeInterface::TRANSVERSE_NODE_ID);
        $node->setName(NodeInterface::TRANSVERSE_NODE_ID);
        $node->setNodeType(NodeInterface::TYPE_TRANSVERSE);
        $node->setSiteId($siteId);
        $node->addArea($area);

        return $node;
    }

    /**
     * Duplicate a node
     *
     * @param NodeInterface   $node
     *
     * @return NodeInterface
     */
    public function saveDuplicatedNode(NodeInterface $node)
    {
        $version = $node->getVersion();
        $documentManager = $this->container->get('doctrine.odm.mongodb.document_manager');
        $documentManager->persist($node);

        $count = 0;

        while ($count < 10) {
            try {
                $count ++;
                $documentManager->flush($node);
            } catch (DuplicateKeyException $e) {
                $node->setVersion($version + $count);
                continue;
            }
            break;
        }

        return $node;
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Manager;

use OpenOrchestra\ModelBundle\Document\Area;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Manager\NodeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class NodeManager
 */
class NodeManager  extends ContainerAware implements NodeManagerInterface
{
    protected $nodeClass;

    /**
     * @param string     $nodeClass
     */
    public function __construct($nodeClass)
    {
        $this->nodeClass = $nodeClass;
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
        $area = new Area();
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
     * @param string $nodeId
     * @param string $siteId
     * @param string $language
     *
     * @return NodeInterface
     */
    public function duplicateNode($nodeId, $siteId, $language)
    {
        $documentManager = $this->container->get('doctrine.odm.mongodb.document_manager');
        $documentManager->getConnection()->initialize();
        $dataBase = $documentManager->getDocumentDatabase($this->nodeClass);
        $return = $dataBase->execute('db.loadServerScripts();return duplicateNode({ nodeId: \''.$nodeId.'\', siteId: \''.$siteId.'\', language: \''.$language.'\' });');

        $newNode = new $this->nodeClass();
        $documentManager->getHydratorFactory()->hydrate($newNode, $return['retval']);

        return $newNode;
    }
}

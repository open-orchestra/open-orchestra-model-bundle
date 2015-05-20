<?php

namespace OpenOrchestra\ModelBundle\Manager;

use OpenOrchestra\ModelBundle\Document\Area;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class NodeManager
 */
class NodeManager
{
    protected $nodeClass;

    /**
     * @param $nodeClass
     */
    public function __construct($nodeClass)
    {
        $this->nodeClass = $nodeClass;
    }

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
}

<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Area;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class TransverseDataGenerator
 */
class TransverseDataGenerator extends AbstractDataGenerator
{
    /**
     * @return Node
     */
    protected function generateNodeFr()
    {
        return $this->generateNodeGlobal("fr", "Page générale");
    }

    /**
     * @return Node
     */
    protected function generateNodeEn()
    {
        return $this->generateNodeGlobal("en", "Global page");
    }

    /**
     * @return Node
     */
    protected function generateNodeDe()
    {
        return $this->generateNodeGlobal("de", "Globale Seite");
    }

    /**
     * @param string $language
     * @param string $name
     *
     * @return Node
     */
    protected function generateNodeGlobal($language, $name)
    {
        $root = new Area();
        $root->setAreaType(AreaInterface::TYPE_ROOT);
        $root->setAreaId(AreaInterface::ROOT_AREA_ID);
        $root->setLabel(AreaInterface::ROOT_AREA_LABEL);

        $mainColumn = $this->createColumnArea('main', 'main');
        $mainRow = $this->createMain(array($mainColumn));
        $root->addArea($mainRow);

        $nodeTransverse = new Node();
        $nodeTransverse->setNodeId(NodeInterface::TRANSVERSE_NODE_ID);
        $nodeTransverse->setMaxAge(1000);
        $nodeTransverse->setNodeType(NodeInterface::TYPE_TRANSVERSE);
        $nodeTransverse->setName($name);
        $nodeTransverse->setBoLabel('Global page');
        $nodeTransverse->setSiteId('2');
        $nodeTransverse->setParentId('-');
        $nodeTransverse->setPath('-');
        $nodeTransverse->setVersion(1);
        $nodeTransverse->setOrder(1);
        $nodeTransverse->setLanguage($language);
        $nodeTransverse->setStatus($this->references['status-draft']);
        $nodeTransverse->setDeleted(false);
        $nodeTransverse->setTemplateId('');
        $nodeTransverse->setTheme('');
        $nodeTransverse->setInFooter(false);
        $nodeTransverse->setInMenu(false);
        $nodeTransverse->setRootArea($root);


        return $nodeTransverse;
    }
}

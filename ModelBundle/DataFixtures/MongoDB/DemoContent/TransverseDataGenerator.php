<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Node;
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
        $mainArea = $this->createArea('main','main','main');

        $nodeTransverse = new Node();
        $nodeTransverse->setNodeId(NodeInterface::TRANSVERSE_NODE_ID);
        $nodeTransverse->setMaxAge(1000);
        $nodeTransverse->setNodeType(NodeInterface::TYPE_TRANSVERSE);
        $nodeTransverse->setName($name);
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
        $nodeTransverse->addArea($mainArea);

        return $nodeTransverse;
    }
}

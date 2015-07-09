<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Area;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class AbstractDataGenerator
 */
abstract class AbstractDataGenerator
{
    protected $references;

    /**
     * Constructor
     *
     * @param array $references
     */
    public function __construct(array $references)
    {
        $this->references = $references;
    }

    /**
     * @param string $language
     *
     * @return Node
     */
    public function generateNode($language)
    {
        if ($language == "fr") {
            return $this->generateNodeFr();
        } else {
            return $this->generateNodeEn();
        }
    }

    /**
     * @param string $label
     * @param string $areaId
     * @param string $htmlClass
     *
     * @return Area
     */
    protected function createArea($label, $areaId, $htmlClass = null)
    {
        $area = new Area();
        $area->setLabel($label);
        $area->setAreaId($areaId);
        if ($htmlClass !== null) {
            $area->setHtmlClass($htmlClass);
        }

        return $area;
    }

    /**
     * @return Area
     */
    protected function createHeader()
    {
        $header = $this->createArea('Header','header','header');
        $header->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));
        $header->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));
        $header->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 5));

        return $header;
    }

    /**
     * @return Area
     */
    protected function createFooter()
    {
        $area = $this->createArea('Footer','footer','footer');
        $area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));
        $area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));

        return $area;
    }

    /**
     * @param boolean $haveBlocks
     * @param string  $htmlClass
     *
     * @return Area
     */
    protected function createModuleArea($haveBlocks = true, $htmlClass = "module-area")
    {
        $area = new Area();
        $area->setLabel('Module area');
        $area->setAreaId('moduleArea');
        $area->setHtmlClass($htmlClass);
        if ($haveBlocks) {
            $area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));
        }

        return $area;
    }

    /**
     * @param array   $areas
     * @param boolean $hasHtmlClass
     *
     * @return Area
     */
    protected function createMain(array $areas, $hasHtmlClass = true)
    {
        $main = new Area();
        $main->setLabel('My main');
        $main->setAreaId('myMain');
        $main->setBoDirection('h');
        if ($hasHtmlClass) {
            $main->setHtmlClass('my-main');
        }
        foreach ($areas as $area) {
            $main->addArea($area);
        }

        return $main;
    }

    /**
     * @return Node
     */
    protected function createBaseNode()
    {
        $node = new Node();
        $node->setMaxAge(1000);
        $node->setNodeType('page');
        $node->setSiteId('2');
        $node->setPath('-');
        $node->setVersion(1);
        $node->setStatus($this->references['status-published']);
        $node->setDeleted(false);
        $node->setTemplateId('');
        $node->setTheme('themePresentation');

        return $node;
    }
}

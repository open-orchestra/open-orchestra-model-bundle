<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\LanguageListStrategy;
use OpenOrchestra\ModelBundle\Document\Area;
use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class AbstractDataGenerator
 */
abstract class AbstractDataGenerator
{
    protected $references;
    protected $version;
    protected $status;

    /**
     * Constructor
     *
     * @param array  $references
     * @param int    $version
     * @param string $status
     */
    public function __construct(array $references, $version = 1, $status = 'status-published')
    {
        $this->references = $references;
        $this->version = $version;
        $this->status = $status;
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
     * @return Node
     */
    abstract protected function generateNodeFr();

    /**
     * @return Node
     */
    abstract protected function generateNodeEn();

    /**
     * @param string $label
     * @param string $areaId
     * @param string $htmlClass
     * @param string $boDirection
     *
     * @return Area
     */
    protected function createArea($label, $areaId, $htmlClass = null, $boDirection = 'v')
    {
        $area = new Area();
        $area->setLabel($label);
        $area->setAreaId($areaId);
        $area->setBoDirection($boDirection);
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
        $header = $this->createArea('Header','header','header','h');
        $header->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));
        $header->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1, 'blockParameter' => array('request.aliasId')));
        $header->addBlock(array('nodeId' => 0, 'blockId' => 0));

        return $header;
    }

    /**
     * @return Area
     */
    protected function createFooter()
    {
        $area = $this->createArea('Footer','footer','footer','h');
        $area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3, 'blockParameter' => array('request.aliasId')));
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
        $siteBlockLanguage = new Block();
        $siteBlockLanguage->setLabel('Language list');
        $siteBlockLanguage->setComponent(LanguageListStrategy::LANGUAGE_LIST);

        $node = new Node();
        $node->setMaxAge(1000);
        $node->setNodeType('page');
        $node->setSiteId('2');
        $node->setPath('-');
        $node->setVersion($this->version);
        $node->setStatus($this->references[$this->status]);
        $node->setDeleted(false);
        $node->setTemplateId('');
        $node->setTheme('themePresentation');
        $node->addBlock($siteBlockLanguage);

        return $node;
    }
}

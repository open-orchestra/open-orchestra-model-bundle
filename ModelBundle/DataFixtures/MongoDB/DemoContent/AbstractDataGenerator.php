<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\LanguageListStrategy;
use OpenOrchestra\ModelBundle\Document\Area;
use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
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
        } else if ($language == "en") {
            return $this->generateNodeEn();
        } else {
            return $this->generateNodeDe();
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
     * @return Node
     */
    abstract protected function generateNodeDe();

    /**
     * @param string      $label
     * @param string      $areaId
     * @param string      $width
     * @param string|null $htmlClass
     *
     * @return AreaInterface
     */
    protected function createColumnArea($label, $areaId, $htmlClass = null, $width = '1')
    {
        $area = new Area();
        $area->setLabel($label);
        $area->setAreaId($areaId);
        $area->setWidth($width);
        $area->setAreaType(AreaInterface::TYPE_COLUMN);

        if ($htmlClass !== null) {
            $area->setHtmlClass($htmlClass);
        }

        return $area;
    }

    /**
     * @return AreaInterface
     */
    protected function createHeader()
    {
        $header = new Area();
        $header->setAreaId('row_header');
        $header->setAreaType(AreaInterface::TYPE_ROW);

        $column = $this->createColumnArea('header', 'column_header');

        $header->addArea($column);


        //$header = $this->createArea('Header','header','header','h');
        //$header->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));
        //$header->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1, 'blockParameter' => array('request.aliasId')));
        //$header->addBlock(array('nodeId' => 0, 'blockId' => 0));

        return $header;
    }

    /**
     * @return AreaInterface
     */
    protected function createFooter()
    {
        $footer = new Area();
        $footer->setAreaId('row_header');
        $footer->setAreaType(AreaInterface::TYPE_ROW);

        $columnMenu = $this->createColumnArea('menu footer', 'column1_footer');
        $columnInfo = $this->createColumnArea('footer information', 'column2_footer');

        $footer->addArea($columnMenu);
        $footer->addArea($columnInfo);
        //$area = $this->createArea('Footer','footer','footer','h');
        //$area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3, 'blockParameter' => array('request.aliasId')));
        //$area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));

        return $footer;
    }

    /**
     * @param boolean $haveBlocks
     * @param string  $htmlClass
     *
     * @return AreaInterface
     */
    protected function createModuleArea($haveBlocks = true, $htmlClass = "module-area")
    {
        $area = $this->createColumnArea('Module area', 'moduleArea', $htmlClass);

        /*if ($haveBlocks) {
            $area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));
        }*/

        return $area;
    }

    /**
     * @param array   $areas
     * @param boolean $hasHtmlClass
     *
     * @return AreaInterface
     */
    protected function createMain(array $areas, $hasHtmlClass = true)
    {
        $main = new Area();
        $main->setAreaId('myMain');
        $main->setAreaType(AreaInterface::TYPE_ROW);

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
        $root = new Area();
        $root->setAreaType(AreaInterface::TYPE_ROOT);
        $root->setAreaId(AreaInterface::ROOT_AREA_ID);
        $root->setLabel(AreaInterface::ROOT_AREA_LABEL);

        /*$siteBlockLanguage = new Block();
        $siteBlockLanguage->setLabel('Language list');
        $siteBlockLanguage->setComponent(LanguageListStrategy::NAME);
        $siteBlockLanguage->addArea(array('nodeId' => 0, 'areaId' => 'header'));*/

        $node = new Node();
        $node->setArea($root);
        $node->setMaxAge(1000);
        $node->setNodeType(NodeInterface::TYPE_DEFAULT);
        $node->setSiteId('2');
        $node->setPath('-');
        $node->setVersion($this->version);
        $node->setStatus($this->references[$this->status]);
        if ('status-published' == $this->status) {
            $node->setCurrentlyPublished(true);
        }
        $node->setDeleted(false);
        $node->setTemplateId('');
        $node->setTheme('themePresentation');
        $node->setDefaultSiteTheme(true);
        //$node->addBlock($siteBlockLanguage);
        $node->setBoDirection('v');

        return $node;
    }
}

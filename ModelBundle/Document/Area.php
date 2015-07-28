<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\AreaInterface;

/**
 * Description of BaseArea
 *
 * @ODM\EmbeddedDocument
 */
class Area implements AreaInterface
{
    /**
     * @var string $label
     *
     * @ODM\Field(type="string")
     */
    protected $label;

    /**
     * @var string $areaId
     *
     * @ODM\Field(type="string")
     */
    protected $areaId;

    /**
     * @var string $htmlClass
     *
     * @ODM\Field(type="string")
     */
    protected $htmlClass;

    /**
     * @var string $boDirection
     *
     * @ODM\Field(type="string")
     */
    protected $boDirection;

    /**
     * @var int $xInGrid
     *
     * @ODM\Field(type="int")
     */
    protected $xInGrid;

    /**
     * @var int $yInGrid
     *
     * @ODM\Field(type="int")
     */
    protected $yInGrid;

    /**
     * @var int $widthInGrid
     *
     * @ODM\Field(type="int")
     */
    protected $widthInGrid;

    /**
     * @var int $heightInGrid
     *
     * @ODM\Field(type="int")
     */
    protected $heightInGrid;

    /**
     * @ODM\Field(type="collection")
     */
    protected $classes = array();

    /**
     * @var ArrayCollection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\AreaInterface")
     */
    protected $subAreas;

    /**
     * @ODM\Field(type="collection")
     */
    protected $blocks = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subAreas = new ArrayCollection();
    }

    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string $label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set areaId
     *
     * @param string $areaId
     */
    public function setAreaId($areaId)
    {
        $this->areaId = $areaId;
    }

    /**
     * Get areaId
     *
     * @return string $areaId
     */
    public function getAreaId()
    {
        return $this->areaId;
    }

    /**
     * Set htmlClass
     *
     * @param string $htmlClass
     */
    public function setHtmlClass($htmlClass)
    {
        $this->htmlClass = $htmlClass;
    }

    /**
     * Get htmlClass
     *
     * @return string $htmlClass
     */
    public function getHtmlClass()
    {
        return $this->htmlClass;
    }

    /**
     * Set boDirection
     *
     * @param string $boDirection
     */
    public function setBoDirection($boDirection)
    {
        $this->boDirection = $boDirection;
    }

    /**
     * Get boDirection
     *
     * @return string $boDirection
     */
    public function getBoDirection()
    {
        return $this->boDirection;
    }

    /**
     * Set xInGrid
     *
     * @param int $xInGrid
     */
    public function setXInGrid($xInGrid)
    {
        $this->xInGrid = $xInGrid;
    }

    /**
     * Get xInGrid
     *
     * @return int $xInGrid
     */
    public function getXInGrid()
    {
        return $this->xInGrid;
    }

    /**
     * Set yInGrid
     *
     * @param int $yInGrid
     */
    public function setYInGrid($yInGrid)
    {
        $this->yInGrid = $yInGrid;
    }

    /**
     * Get yInGrid
     *
     * @return int $yInGrid
     */
    public function getYInGrid()
    {
        return $this->yInGrid;
    }

    /**
     * Set widthInGrid
     *
     * @param int $widthInGrid
     */
    public function setWidthInGrid($widthInGrid)
    {
        $this->widthInGrid = $widthInGrid;
    }

    /**
     * Get widthInGrid
     *
     * @return int $widthInGrid
     */
    public function getWidthInGrid()
    {
        return $this->widthInGrid;
    }

    /**
     * Set heightInGrid
     *
     * @param int $heightInGrid
     */
    public function setHeightInGrid($heightInGrid)
    {
        $this->heightInGrid = $heightInGrid;
    }

    /**
     * Get heightInGrid
     *
     * @return int $heightInGrid
     */
    public function getHeightInGrid()
    {
        return $this->heightInGrid;
    }

    /**
     * Set classes
     *
     * @deprecated use setHtmlClass instead
     *
     * @param array $classes
     */
    public function setClasses(array $classes)
    {
        $this->classes = $classes;
    }

    /**
     * Get classes
     *
     * @deprecated use getHtmlClass instead
     *
     * @return array $classes
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Add subArea
     *
     * @param AreaInterface $subArea
     */
    public function addArea(AreaInterface $subArea)
    {
        $this->subAreas->add($subArea);
    }

    /**
     * Remove subArea
     *
     * @param AreaInterface $subArea
     */
    public function removeArea(AreaInterface $subArea)
    {
        $this->subAreas->removeElement($subArea);
    }

    /**
     * Remove subArea by areaId
     *
     * @param string $areaId
     */
    public function removeAreaByAreaId($areaId)
    {
        foreach ($this->getAreas() as $key => $area) {
            if ($areaId == $area->getAreaId()) {
                $this->getAreas()->remove($key);
                break;
            }
        }
    }

    /**
     * @param Collection $areas
     */
    public function setAreas(Collection $areas)
    {
        $this->areas = new ArrayCollection();
        foreach ($areas as $area) {
            $this->areas->add($area);
        }
    }

    /**
     * Get subAreas
     *
     * @return ArrayCollection $subAreas
     */
    public function getAreas()
    {
        return $this->subAreas;
    }

    /**
     * Set blocks
     *
     * @param array $blocks
     */
    public function setBlocks(array $blocks)
    {
        $this->blocks = $blocks;
    }

    /**
     * @param array $blockDescription
     */
    public function addBlock(array $blockDescription)
    {
        $this->blocks[] = $blockDescription;
    }

    /**
     * Get blocks
     *
     * @return array $blocks
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
}

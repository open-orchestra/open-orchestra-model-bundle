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
     * @var int $gridX
     *
     * @ODM\Field(type="int")
     */
    protected $gridX;

    /**
     * @var int $gridY
     *
     * @ODM\Field(type="int")
     */
    protected $gridY;

    /**
     * @var int $gridWidth
     *
     * @ODM\Field(type="int")
     */
    protected $gridWidth;

    /**
     * @var int $gridHeight
     *
     * @ODM\Field(type="int")
     */
    protected $gridHeight;

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
        $this->initializeCollections();
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
     * Set gridX
     *
     * @param int $gridX
     */
    public function setgridX($gridX)
    {
        $this->gridX = $gridX;
    }

    /**
     * Get gridX
     *
     * @return int $gridX
     */
    public function getGridX()
    {
        return $this->gridX;
    }

    /**
     * Set gridY
     *
     * @param int $gridY
     */
    public function setGridY($gridY)
    {
        $this->gridY = $gridY;
    }

    /**
     * Get gridY
     *
     * @return int $gridY
     */
    public function getGridY()
    {
        return $this->gridY;
    }

    /**
     * Set gridWidth
     *
     * @param int $gridWidth
     */
    public function setGridWidth($gridWidth)
    {
        $this->gridWidth = $gridWidth;
    }

    /**
     * Get gridWidth
     *
     * @return int $gridWidth
     */
    public function getGridWidth()
    {
        return $this->gridWidth;
    }

    /**
     * Set gridHeight
     *
     * @param int $gridHeight
     */
    public function setGridHeight($gridHeight)
    {
        $this->gridHeight = $gridHeight;
    }

    /**
     * Get gridHeight
     *
     * @return int $gridHeight
     */
    public function getGridHeight()
    {
        return $this->gridHeight;
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

    /**
     * Initialize collections
     */
    protected function initializeCollections()
    {
        $this->subAreas = new ArrayCollection();
    }

    /**
     * Initialize collections on clone
     */
    public function __clone()
    {
        $this->initializeCollections();
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Description of AreaFlex
 *
 * @ODM\EmbeddedDocument
 */
class AreaFlex implements AreaFlexInterface
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
     * @var string $areaType
     *
     * @ODM\Field(type="string")
     */
    protected $areaType;

    /**
     * @var string $width
     *
     * @ODM\Field(type="string")
     */
    protected $width;

    /**
     * @var Collection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\AreaFlexInterface")
     */
    protected $subAreas;

    /**
     * @var string $htmlClass
     *
     * @ODM\Field(type="string")
     */
    protected $htmlClass;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subAreas = new ArrayCollection();
    }

    /**
     * Set area type
     *
     * @param string $areaType
     */
    public function setAreaType($areaType)
    {
        $this->areaType = $areaType;
    }

    /**
     * Get area type
     *
     * @return string $areaType
     */
    public function getAreaType()
    {
        return $this->areaType;
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
     * Add subArea
     *
     * @param AreaFlexInterface $subArea
     */
    public function addArea(AreaFlexInterface $subArea)
    {
        $this->subAreas->add($subArea);
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
            $area->removeAreaByAreaId($areaId);
        }
    }

    /**
     * @param Collection $areas
     */
    public function setAreas(Collection $areas)
    {
        $this->subAreas = new ArrayCollection();
        foreach ($areas as $area) {
            $this->subAreas->add($area);
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
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
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
}

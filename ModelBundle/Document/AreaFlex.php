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
     * @var Collection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\AreaFlexInterface")
     */
    protected $subAreas;

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
     * Remove subArea
     *
     * @param AreaFlexInterface $subArea
     */
    public function removeArea(AreaFlexInterface $subArea)
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

}

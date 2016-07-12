<?php

namespace OpenOrchestra\ModelBundle\Repository\RepositoryTrait;

/**
 * Trait AreaFinderTrait
 */
trait AreaFinderTrait
{
    /**
     * @param \OpenOrchestra\ModelInterface\Model\AreaContainerInterface $area
     * @param string                                                     $areaId
     *
     * @return null|\OpenOrchestra\ModelInterface\Model\AreaInterface
     */
    public function findAreaByAreaId($area, $areaId)
    {
        foreach ($area->getAreas() as $subArea) {
            if ($areaId == $subArea->getAreaId()) {
                return $subArea;
            }
            if ($selectedArea = $this->findAreaByAreaId($subArea, $areaId)) {
                return $selectedArea;
            }
        }

        return null;
    }
}

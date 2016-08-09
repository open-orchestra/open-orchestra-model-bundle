<?php

namespace OpenOrchestra\ModelBundle\Repository\RepositoryTrait;

use OpenOrchestra\ModelInterface\Model\AreaContainerInterface;

/**
 * Trait AreaFinderTrait
 */
trait AreaFinderTrait
{
    /**
     * @param AreaContainerInterface $area
     * @param string                 $areaId
     *
     * @return null|\OpenOrchestra\ModelInterface\Model\AreaInterface
     */
    public function findAreaByAreaId(AreaContainerInterface $area, $areaId)
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

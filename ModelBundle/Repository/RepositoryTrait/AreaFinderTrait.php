<?php

namespace PHPOrchestra\ModelBundle\Repository\RepositoryTrait;

use PHPOrchestra\ModelInterface\Model\AreaContainerInterface;

/**
 * Trait AreaFinderTrait
 */
trait AreaFinderTrait
{
    /**
     * @param AreaContainerInterface $area
     * @param string                 $areaId
     *
     * @return null|AreaInterface
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

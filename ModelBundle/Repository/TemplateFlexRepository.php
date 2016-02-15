<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\TemplateFlexRepositoryInterface;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class TemplateFlexRepository
 */
class TemplateFlexRepository extends AbstractAggregateRepository implements FieldAutoGenerableRepositoryInterface, TemplateFlexRepositoryInterface
{
    /**
     * @param string $templateId
     *
     * @return TemplateFlexInterface
     */
    public function findOneByTemplateId($templateId)
    {
        return $this->findOneBy(array('templateId' => $templateId));
    }

    /**
     * @param $template TemplateFlexInterface
     * @param string    $areaId
     *
     * @return null|AreaFlexInterface
     */
    public function findAreaInTemplateByAreaId(TemplateFlexInterface $template, $areaId)
    {
        $rootArea = $template->getArea();
        if ($areaId === $rootArea->getAreaId()) {
            return $rootArea;
        }

        return $this->findAreaFlexByAreaId($rootArea, $areaId);
    }

    /**
     * @param boolean $deleted
     *
     * @return array
     */
    public function findByDeleted($deleted)
    {
        return $this->findBy(array('deleted' => $deleted));
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function testUniquenessInContext($name)
    {
        return $this->findOneByName($name) !== null;
    }

    /**
     * @param AreaFlexInterface $area
     * @param $areaId
     *
     * @return AreaFlexInterface|null
     */
    protected function findAreaFlexByAreaId(AreaFlexInterface $area, $areaId)
    {
        foreach ($area->getAreas() as $subArea) {
            if ($areaId === $subArea->getAreaId()) {
                return $subArea;
            }
            if ($selectedArea = $this->findAreaFlexByAreaId($subArea, $areaId)) {
                return $selectedArea;
            }
        }

        return null;
    }
}

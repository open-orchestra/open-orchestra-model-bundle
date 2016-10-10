<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\AreaFinderTrait;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class TemplateRepository
 */
class TemplateRepository extends AbstractAggregateRepository implements FieldAutoGenerableRepositoryInterface, TemplateRepositoryInterface
{
    use AreaFinderTrait;

    /**
     * @param $template TemplateInterface
     * @param string    $areaId
     *
     * @return null|AreaInterface
     */
    public function findAreaInTemplateByAreaId(TemplateInterface $template, $areaId)
    {
        $rootArea = $template->getRootArea();
        if ($areaId === $rootArea->getAreaId()) {
            return $rootArea;
        }

        return $this->findAreaByAreaId($rootArea, $areaId);
    }

    /**
     * @param string $templateId
     *
     * @return TemplateInterface
     */
    public function findOneByTemplateId($templateId)
    {
        return $this->findOneBy(array('templateId' => $templateId));
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
}

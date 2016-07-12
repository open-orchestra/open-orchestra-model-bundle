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
        $rootArea = $template->getArea();
        if ($areaId === $rootArea->getAreaId()) {
            return $rootArea;
        }

        return $this->findAreaByAreaId($rootArea, $areaId);
    }

    /**
     * @param string $templateId
     * @param string $areaId
     *
     * @return AreaInterface|null
     * @deprecated will be removed in 2.0
     */
    public function findAreaByTemplateIdAndAreaId($templateId, $areaId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);
        $template = $this->findOneByTemplateId($templateId);

        return $this->findAreaByAreaId($template, $areaId);
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

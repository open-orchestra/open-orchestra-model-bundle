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
     * @param string $templateId
     * @param string $areaId
     *
     * @return AreaInterface|null
     */
    public function findAreaByTemplateIdAndAreaId($templateId, $areaId)
    {
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

<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\TemplateInterface;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\TemplateFlexRepositoryInterface;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class TemplateFlexRepository
 */
class TemplateFlexRepository extends AbstractAggregateRepository implements FieldAutoGenerableRepositoryInterface,  TemplateFlexRepositoryInterface
{
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

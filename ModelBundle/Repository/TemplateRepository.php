<?php

namespace PHPOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use PHPOrchestra\ModelBundle\Repository\RepositoryTrait\AreaFinderTrait;
use PHPOrchestra\ModelInterface\Model\AreaInterface;
use PHPOrchestra\ModelInterface\Model\TemplateInterface;
use PHPOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;
use PHPOrchestra\ModelInterface\Model\AreaContainerInterface;

/**
 * Class TemplateRepository
 */
class TemplateRepository extends DocumentRepository implements FieldAutoGenerableRepositoryInterface, TemplateRepositoryInterface
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
    public function testUnicityInContext($name)
    {
        return $this->findOneByName($name) !== null;
    }
}

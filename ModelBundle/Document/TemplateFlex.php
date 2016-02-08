<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;
use OpenOrchestra\MongoTrait\SoftDeleteable;

/**
 * Description of Template Flex
 *
 * @ODM\Document(
 *   collection="template_flex",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\TemplateFlexRepository"
 * )
 * @ORCHESTRA\Document(
 *   generatedField="templateId",
 *   sourceField="name",
 *   serviceName="open_orchestra_model.repository.template_flex",
 * )
 */

class TemplateFlex implements TemplateFlexInterface
{
    use SoftDeleteable;

    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $templateId
     *
     * @ODM\Field(type="string")
     */
    protected $templateId;

    /**
     * @var string $siteId
     *
     * @ODM\Field(type="string")
     */
    protected $siteId;

    /**
     * @var string $name
     *
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var AreaFlexInterface
     *
     * @ODM\EmbedOne(targetDocument="OpenOrchestra\ModelInterface\Model\AreaFlexInterface")
     */
    protected $area;

    /**
     * @param AreaFlexInterface $area
     */
    public function setArea(AreaFlexInterface $area)
    {
        $this->area = $area;
    }

    /**
     * @return AreaFlexInterface
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * @return string
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param int $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    /**
     * @return string
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }
}

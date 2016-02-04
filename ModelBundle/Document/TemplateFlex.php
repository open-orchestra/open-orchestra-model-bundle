<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\AreaFlexInterface")
     */
    protected $areas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->areas = new ArrayCollection();
    }

    /**
     * @param AreaFlexInterface $area
     */
    public function addArea(AreaFlexInterface $area)
    {
        $this->areas->add($area);
    }

    /**
     * @param AreaFlexInterface $area
     */
    public function removeArea(AreaFlexInterface $area)
    {
        $this->areas->removeElement($area);
    }

    /**
     * Remove subArea by areaId
     *
     * @param string $areaId
     */
    public function removeAreaByAreaId($areaId)
    {
        foreach ($this->getAreas() as $key => $area) {
            if ($areaId == $area->getAreaId()) {
                $this->getAreas()->remove($key);
                break;
            }
        }
    }

    /**
     * @param Collection $areas
     */
    public function setAreas(Collection $areas)
    {
        $this->areas = new ArrayCollection();
        foreach ($areas as $key => $area) {
            $this->areas->add($area);
        }
    }

    /**
     * @return Collection
     */
    public function getAreas()
    {
        return $this->areas;
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

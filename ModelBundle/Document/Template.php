<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;
use OpenOrchestra\MongoTrait\SoftDeleteable;
use OpenOrchestra\MongoTrait\Versionable;

/**
 * Description of Template
 *
 * @ODM\Document(
 *   collection="template",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\TemplateRepository"
 * )
 * @ORCHESTRA\Document(
 *   generatedField="templateId",
 *   sourceField="name",
 *   serviceName="open_orchestra_model.repository.template",
 * )
 */

class Template implements TemplateInterface
{
    use Versionable;
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
     * @var string
     *
     * @ODM\Field(type="string")
     * @deprecated will be removed in 2.0
     */
    protected $language;

    /**
     * @var AreaInterface
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\AreaInterface")
     * @deprecated will be removed in 2.0
     */
    protected $areas;

    /**
     * @var AreaInterface
     *
     * @ODM\EmbedOne(targetDocument="OpenOrchestra\ModelInterface\Model\AreaInterface")
     */
    protected $rootArea;

    /**
     * @var string $boDirection
     *
     * @ODM\Field(type="string")
     * @deprecated will be removed in 2.0
     */
    protected $boDirection;

    /**
     * @var ArrayCollection
     * @deprecated will be removed in 2.0
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\BlockInterface")
     */
    protected $blocks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->blocks = new ArrayCollection();
        $this->areas = new ArrayCollection();
    }

    /**
     * @param AreaInterface $area
     * @deprecated will be removed in 2.0
     */
    public function addArea(AreaInterface $area)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);
        $this->areas->add($area);
    }

    /**
     * @param AreaInterface $area
     * @deprecated will be removed in 2.0
     */
    public function removeArea(AreaInterface $area)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);
        $this->areas->removeElement($area);
    }

    /**
     * Remove subArea by areaId
     *
     * @param string $areaId
     * @deprecated will be removed in 2.0
     */
    public function removeAreaByAreaId($areaId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);
        foreach ($this->getAreas() as $key => $area) {
            if ($areaId == $area->getAreaId()) {
                $this->getAreas()->remove($key);
                break;
            }
        }
    }

    /**
     * @param Collection $areas
     * @deprecated will be removed in 2.0
     */
    public function setAreas(Collection $areas)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);
        $this->areas = new ArrayCollection();
        foreach ($areas as $key => $area) {
            $this->areas->add($area);
        }
    }

    /**
     * @return Collection
     * @deprecated will be removed in 2.0
     */
    public function getAreas()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);
        return $this->areas;
    }

    /**
     * @param AreaInterface $rootArea
     */
    public function setRootArea(AreaInterface $rootArea)
    {
        $this->rootArea = $rootArea;
    }

    /**
     * @return AreaInterface
     */
    public function getRootArea()
    {
        return $this->rootArea;
    }

    /**
     * @param BlockInterface $block
     * @deprecated will be removed in 2.0
     */
    public function addBlock(BlockInterface $block)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $this->blocks->add($block);
    }

    /**
     * @param BlockInterface $block
     * @deprecated will be removed in 2.0
     */
    public function removeBlock(BlockInterface $block)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $this->blocks->removeElement($block);
    }

    /**
     * Remove block with index $key
     *
     * @param string $key
     *
     * @deprecated will be removed in 2.0
     */
    public function removeBlockWithKey($key)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $this->blocks->remove($key);
    }

    /**
     * @return array
     * @deprecated will be removed in 2.0
     */
    public function getBlocks()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        return $this->blocks;
    }

    /**
     * @param string $boDirection
     * @deprecated will be removed in 2.0
     */
    public function setBoDirection($boDirection)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);
        $this->boDirection = $boDirection;
    }

    /**
     * @return string
     * @deprecated will be removed in 2.0
     */
    public function getBoDirection()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);
        return $this->boDirection;
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
     * @param string $language
     * @deprecated will be removed in 2.0
     */
    public function setLanguage($language)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);
        $this->language = $language;
    }

    /**
     * @return string
     * @deprecated will be removed in 2.0
     */
    public function getLanguage()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        return $this->language;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }
}

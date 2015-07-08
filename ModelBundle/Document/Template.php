<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Mapping\Annotations as ORCHESTRA;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;
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
     * @var boolean
     *
     * @ODM\Field(type="boolean")
     */
    protected $deleted = false;

    /**
     * @var AreaInterface
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\AreaInterface")
     */
    protected $areas;

    /**
     * @var string $boDirection
     *
     * @ODM\Field(type="string")
     */
    protected $boDirection;

    /**
     * @var ArrayCollection
     *
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
     */
    public function addArea(AreaInterface $area)
    {
        $this->areas->add($area);
    }

    /**
     * @param AreaInterface $area
     */
    public function removeArea(AreaInterface $area)
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
     * @return AreaInterface
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * @param BlockInterface $block
     */
    public function addBlock(BlockInterface $block)
    {
        $this->blocks->add($block);
    }

    /**
     * @param BlockInterface $block
     */
    public function removeBlock(BlockInterface $block)
    {
        $this->blocks->removeElement($block);
    }

    /**
     * @return ArrayCollection
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param string $boDirection
     */
    public function setBoDirection($boDirection)
    {
        $this->boDirection = $boDirection;
    }

    /**
     * @return string
     */
    public function getBoDirection()
    {
        return $this->boDirection;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
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

<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Mapping\Annotations as ORCHESTRA;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use OpenOrchestra\MongoTrait\Statusable;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use OpenOrchestra\MongoTrait\Cacheable;
use OpenOrchestra\MongoTrait\Metaable;
use OpenOrchestra\MongoTrait\Sitemapable;
use OpenOrchestra\MongoTrait\Schemeable;
use OpenOrchestra\MongoTrait\Versionable;

/**
 * Description of Node
 *
 * @ODM\Document(
 *   collection="node",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\NodeRepository"
 * )
 * @ODM\Indexes({
 *  @ODM\Index(keys={"nodeId"="asc"})
 * })
 * @ORCHESTRA\Document(
 *   generatedField="nodeId",
 *   sourceField="name",
 *   serviceName="open_orchestra_model.repository.node",
 * )
 */
class Node implements NodeInterface
{
    use TimestampableDocument;
    use BlameableDocument;
    use Versionable;
    use Sitemapable;
    use Schemeable;
    use Statusable;
    use Cacheable;
    use Metaable;

    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $nodeId
     *
     * @ODM\Field(type="string")
     */
    protected $nodeId;

    /**
     * @var string $nodeType
     *
     * @ODM\Field(type="string")
     */
    protected $nodeType = NodeInterface::TYPE_DEFAULT;

    /**
     * @var string $siteId
     *
     * @ODM\Field(type="string")
     */
    protected $siteId;

    /**
     * @var string $parentId
     *
     * @ODM\Field(type="string")
     */
    protected $parentId;

    /**
     * @var string $path
     *
     * @ODM\Field(type="string")
     */
    protected $path;

    /**
     * @var string $name
     *
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var string $language
     *
     * @ODM\Field(type="string")
     */
    protected $language;

    /**
     * @var boolean
     *
     * @ODM\Field(type="boolean")
     */
    protected $deleted = false;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $templateId;

    /**
     * @var string $theme
     *
     * @ODM\Field(type="string")
     */
    protected $theme;

    /**
     * @var boolean
     *
     * @ODM\Field(type="boolean")
     */
    protected $inMenu;

    /**
     * @var boolean
     *
     * @ODM\Field(type="boolean")
     */
    protected $inFooter;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $role;

    /**
     * @var ArrayCollection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\AreaInterface")
     */
    protected $areas;

    /**
     * @var BlockInterface
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\BlockInterface")
     */
    protected $blocks;

    /**
     * @var int
     *
     * @ODM\Field(type="int")
     */
    protected $order = 0;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $routePattern;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initializeCollections();
    }

    /**
     * Get id
     *
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nodeId
     *
     * @param string $nodeId
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * Get nodeId
     *
     * @return string
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * Set nodeType
     *
     * @param string $nodeType
     */
    public function setNodeType($nodeType)
    {
        $this->nodeType = $nodeType;
    }

    /**
     * Get nodeType
     *
     * @return string $nodeType
     */
    public function getNodeType()
    {
        return $this->nodeType;
    }

    /**
     * Set siteId
     *
     * @param string $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * Get siteId
     *
     * @return string $siteId
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * Set parentId
     *
     * @param string $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Get parentId
     *
     * @return string $parentId
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string $path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set language
     *
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get language
     *
     * @return string $language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * Get deleted
     *
     * @return boolean $deleted
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set templateId
     *
     * @param string $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    /**
     * Get templateId
     *
     * @return string $templateId
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Set theme
     *
     * @param string $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * Get theme
     *
     * @return string $theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Add block
     *
     * @param BlockInterface $block
     */
    public function addBlock(BlockInterface $block)
    {
        $this->blocks->add($block);
    }

    /**
     * Set blocks
     *
     * @param Collection $blocks
     */
    public function setBlocks(Collection $blocks)
    {
        $this->blocks->clear();
        foreach($blocks as $block){
            $this->blocks->add($block);
        }
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool|int|mixed|string
     */
    public function getBlockIndex(BlockInterface $block)
    {
        return $this->blocks->indexOf($block);
    }

    /**
     * @param int            $key
     * @param BlockInterface $block
     */
    public function setBlock($key, BlockInterface $block)
    {
        $this->blocks->set($key, $block);
    }

    /**
     * @param int $key
     *
     * @return BlockInterface
     */
    public function getBlock($key)
    {
        return $this->blocks->get($key);
    }

    /**
     * Remove block
     *
     * @param BlockInterface $block
     */
    public function removeBlock(BlockInterface $block)
    {
        $this->blocks->removeElement($block);
    }

    /**
     * Get blocks
     *
     * @return ArrayCollection $blocks
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param AreaInterface $area
     */
    public function addArea(AreaInterface $area)
    {
        $this->areas->add($area);
    }

    /**
     * @param Collection $areas
     */
    public function setAreas(Collection $areas)
    {
        $this->areas->clear();
        foreach($areas as $area){
            $this->areas->add($area);
        }
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
     * @return ArrayCollection
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * @param boolean $inFooter
     */
    public function setInFooter($inFooter)
    {
        $this->inFooter = $inFooter;
    }

    /**
     * @return boolean
     *
     * @deprecated use isInFooter
     */
    public function getInFooter()
    {
        return $this->isInFooter();
    }

    /**
     * @return boolean
     */
    public function isInFooter()
    {
        return $this->inFooter;
    }

    /**
     * @param boolean $inMenu
     */
    public function setInMenu($inMenu)
    {
        $this->inMenu = $inMenu;
    }

    /**
     * @return boolean
     */
    public function isInMenu()
    {
        return $this->inMenu;
    }

    /**
     * @return boolean
     *
     * @deprecated use isInMenu
     */
    public function getInMenu()
    {
        return $this->isInMenu();
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Clone method
     */
    public function __clone()
    {
        if (!is_null($this->id)) {
            $this->id = null;
            $this->initializeCollections();
            $this->setCreatedAt(new \DateTime());
            $this->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order =$order;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getRoutePattern()
    {
        return $this->routePattern;
    }

    /**
     * @param string $routePattern
     */
    public function setRoutePattern($routePattern)
    {
        $this->routePattern = $routePattern;
    }

    /**
     * Initialize collections
     */
    protected function initializeCollections()
    {
        $this->areas = new ArrayCollection();
        $this->blocks = new ArrayCollection();
    }

    /**
     * @return boolean
     */
    public function isEditable()
    {
        $isEditable = true;
        if ($this->getNodeId() != self::TRANSVERSE_NODE_ID && $this->getStatus() instanceof StatusInterface) {
            $isEditable = !$this->getStatus()->isPublished();
        }

        return $isEditable;
    }
}

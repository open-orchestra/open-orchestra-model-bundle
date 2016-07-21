<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use OpenOrchestra\MongoTrait\SoftDeleteable;
use OpenOrchestra\MongoTrait\Statusable;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
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
 *  @ODM\Index(keys={"nodeId"="asc", "siteId"="asc", "language"="asc", "deleted"="asc", "status.published"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"siteId"="asc", "language"="asc", "nodeType"="asc", "status.published"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"siteId"="asc", "deleted"="asc", "nodeType"="asc", "status.published"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"siteId"="asc", "language"="asc", "deleted"="asc", "status.published"="asc", "inFooter"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"siteId"="asc", "language"="asc", "deleted"="asc", "status.published"="asc", "inMenu"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"deleted"="asc", "status.published"="asc", "updatedAt"="desc"}),
 *  @ODM\Index(keys={"nodeId"="asc", "siteId"="asc", "language"="asc", "deleted"="asc", "version"="desc"})
 * })
 * @ODM\UniqueIndex(keys={"nodeId"="asc", "siteId"="asc", "version"="asc", "language"="asc"})
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
    use SoftDeleteable;

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
     * @var string $boLabel
     *
     * @ODM\Field(type="string")
     * @deprecated will be removed in 2.0
     */
    protected $boLabel;

    /**
     * @var string $language
     *
     * @ODM\Field(type="string")
     */
    protected $language;

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
     * @var string $themeSiteDefault
     *
     * @ODM\Field(type="boolean")
     */
    protected $themeSiteDefault;

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
     * @var AreaInterface
     *
     * @ODM\EmbedOne(targetDocument="OpenOrchestra\ModelInterface\Model\AreaInterface")
     */
    protected $rootArea;

    /**
     * @var ArrayCollection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\AreaInterface")
     * @deprecated will be removed in 2.0
     */
    protected $areas;

    /**
     * @var Collection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\BlockInterface", strategy="set")
     */
    protected $blocks;

    /**
     * @var string $boDirection
     *
     * @ODM\Field(type="string")
     * @deprecated will be removed in 2.0
     */
    protected $boDirection;

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
     * @var string $metaKeywords
     *
     * @ODM\Field(type="string")
     */
    protected $metaKeywords;

    /**
     * @var string $metaDescription
     *
     * @ODM\Field(type="string")
     */
    protected $metaDescription;

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
     * @return string
     */
    public function getBoLabel()
    {
        return $this->boLabel;
    }

    /**
     * @param string $boLabel
     */
    public function setBoLabel($boLabel)
    {
        $this->boLabel = $boLabel;
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
     * Set default theme site
     *
     * @param boolean $themeSiteDefault
     */
    public function setDefaultSiteTheme($themeSiteDefault)
    {
        $this->themeSiteDefault = $themeSiteDefault;
    }

    /**
     * Has default site theme
     *
     * @return boolean $themeSiteDefault
     */
    public function hasDefaultSiteTheme()
    {
        return $this->themeSiteDefault;
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
        foreach ($blocks as $block) {
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
     * Remove block with index $key
     *
     * @param string $key
     */
    public function removeBlockWithKey($key)
    {
        $this->blocks->remove($key);
    }

    /**
     * Get blocks
     *
     * @return array $blocks
     */
    public function getBlocks()
    {
        return $this->blocks;
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
     * @param AreaInterface $area
     *
     * @deprecated will be removed in 2.0
     */
    public function addArea(AreaInterface $area)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $this->areas->add($area);
    }

    /**
     * @param Collection $areas
     * @deprecated will be removed in 2.0
     *
     */
    public function setAreas(Collection $areas)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $this->areas = new ArrayCollection();
        foreach ($areas as $area) {
            $this->areas->add($area);
        }
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
     * @return ArrayCollection
     * @deprecated will be removed in 2.0
     */
    public function getAreas()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        return $this->rootArea->getAreas();
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
     * Initialize collections
     */
    protected function initializeCollections()
    {
        $this->areas = new ArrayCollection();
        $this->blocks = new ArrayCollection();
    }

    /**
     * @param string $metaKeywords
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }
}

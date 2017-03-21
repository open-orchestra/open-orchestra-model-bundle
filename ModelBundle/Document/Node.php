<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use OpenOrchestra\MongoTrait\Keywordable;
use OpenOrchestra\MongoTrait\SoftDeleteable;
use OpenOrchestra\MongoTrait\Statusable;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\MongoTrait\Cacheable;
use OpenOrchestra\MongoTrait\Metaable;
use OpenOrchestra\MongoTrait\Sitemapable;
use OpenOrchestra\MongoTrait\Schemeable;
use OpenOrchestra\MongoTrait\UseTrackable;
use OpenOrchestra\MongoTrait\Versionable;
use OpenOrchestra\MongoTrait\Historisable;
use OpenOrchestra\MongoTrait\AutoPublishable;
use OpenOrchestra\ModelInterface\Model\AreaInterface;

/**
 * Description of Node
 *
 * @ODM\Document(
 *   collection="node",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\NodeRepository"
 * )
 * @ODM\Indexes({
 *  @ODM\Index(keys={"nodeId"="asc", "siteId"="asc", "language"="asc", "deleted"="asc", "status.publishedState"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"siteId"="asc", "language"="asc", "nodeType"="asc", "status.publishedState"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"siteId"="asc", "deleted"="asc", "nodeType"="asc", "status.publishedState"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"siteId"="asc", "language"="asc", "deleted"="asc", "status.publishedState"="asc", "inFooter"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"siteId"="asc", "language"="asc", "deleted"="asc", "status.publishedState"="asc", "inMenu"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"deleted"="asc", "status.publishedState"="asc", "updatedAt"="desc"}),
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
    use Historisable;
    use Keywordable;
    use AutoPublishable;
    use UseTrackable;

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
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $template;

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
     * @var string $metaDescription
     *
     * @ODM\Field(type="string")
     */
    protected $metaDescription;

    /**
     * @var string $seoTitle
     *
     * @ODM\Field(type="string")
     */
    protected $seoTitle;

    /**
     * @var string $canonicalPage
     *
     * @ODM\Field(type="string")
     */
    protected $canonicalPage;

    /**
     * @var string $specialPageName
     *
     * @ODM\Field(type="string")
     */
    protected $specialPageName;

    /**
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\AreaInterface", strategy="set")
    */
    protected $areas;

    /**
     * @var ArrayCollection
     *
     * @ODM\Field(type="collection")
     */
    protected $frontRoles;


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
     * Set template
     *
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Get template
     *
     * @return string $template
     */
    public function getTemplate()
    {
        return $this->template;
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
        $this->order = $order;
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

    /**
     * @param string $seoTitle
     */
    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;
    }

    /**
     * @return string
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * @param string $canonicalPage
     */
    public function setCanonicalPage($canonicalPage)
    {
        $this->canonicalPage = $canonicalPage;
    }

    /**
     * @return string
     */
    public function getCanonicalPage()
    {
        return $this->canonicalPage;
    }

    /**
     * @param string $specialPageName
     */
    public function setSpecialPageName($specialPageName)
    {
        $this->specialPageName = $specialPageName;
    }

    /**
     * @return string
     */
    public function getSpecialPageName()
    {
        return $this->specialPageName;
    }

    /**
     * @param string        $areaId
     * @param AreaInterface $area
     */
    public function setArea($areaId, AreaInterface $area)
    {
        $this->areas->set($areaId, $area);
    }

    /**
     * Get areas
     *
     * @return \Doctrine\Common\Collections\Collection $areas
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * Get area
     * @param string        $areaId
     *
     * @return AreaInterface $area
     */
    public function getArea($areaId)
    {
        return $this->areas->get($areaId);
    }

    /**
     * @param ArrayCollection $frontRoles
     */
    public function setFrontRoles(ArrayCollection $frontRoles)
    {
        $this->frontRoles = $frontRoles;
    }

    /**
     * @return ArrayCollection
     */
    public function getFrontRoles()
    {
        return $this->frontRoles;
    }

    /**
     * Initialize collections
     */
    protected function initializeCollections()
    {
        $this->initializeHistories();
        $this->initializeKeywords();
        $this->areas = new ArrayCollection();
        $this->frontRoles = new ArrayCollection();
    }
}

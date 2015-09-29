<?php

namespace OpenOrchestra\ModelBundle\Document;

use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;

/**
 * Class Redirection
 *
 * @ODM\Document(
 *   collection="redirection",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\RedirectionRepository"
 * )
 */
class Redirection implements RedirectionInterface
{
    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $siteId;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="locale")
     */
    protected $locale;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="site_name")
     */
    protected $siteName;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="route_pattern")
     */
    protected $routePattern;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $nodeId;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="redirection")
     */
    protected $url;

    /**
     * @var boolean
     *
     * @ODM\Field(type="boolean")
     * @ORCHESTRA\Search(key="permanent", type="boolean")
     */
    protected $permanent;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSiteId()
    {
        return (string) $this->siteId;
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
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * @param string $siteName
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;
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
     * @return string
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @param string $nodeId
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return boolean
     */
    public function isPermanent()
    {
        return $this->permanent;
    }

    /**
     * @param boolean $permanent
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;
    }
}

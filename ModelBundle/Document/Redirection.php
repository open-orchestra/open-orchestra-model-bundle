<?php

namespace OpenOrchestra\ModelBundle\Document;

use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

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
     */
    protected $aliasId;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $nodeId;

    /**
     * @var string
     *
     * @ODM\Field(type="hash")
     */
    protected $wildcard;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $routePattern;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $url;

    /**
     * @var boolean
     *
     * @ODM\Field(type="boolean")
     */
    protected $permanent;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->wildcard = array();
    }

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
    public function getAliasId()
    {
        return $this->aliasId;
    }

    /**
     * @param string $aliasId
     */
    public function setAliasId($aliasId)
    {
        $this->aliasId = $aliasId;
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
     * @return array
     */
    public function getWildcard()
    {
        return $this->wildcard;
    }

    /**
     * @param array $wildcard
     */
    public function setWildcard($wildcard)
    {
        $this->wildcard = array();
        foreach ($wildcard as $key => $item) {
            $this->wildcard[$key] = $item;
        }
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

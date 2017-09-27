<?php

namespace OpenOrchestra\ModelBundle\Document;

use OpenOrchestra\ModelInterface\Model\InternalUrlInterface;
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
    protected $locale;

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
     * @ODM\EmbedOne(targetDocument="OpenOrchestra\ModelInterface\Model\InternalUrlInterface")
     */
    protected $internalUrl;


    /**
     * @var boolean
     *
     * @ODM\Field(type="boolean")
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
     * @return string
     */
    public function getInternalUrl()
    {
        return $this->internalUrl;
    }

    /**
     * @param InternalUrlInterface $internalUrl
     */
    public function setInternalUrl(InternalUrlInterface $internalUrl)
    {
        $this->internalUrl = $internalUrl;
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

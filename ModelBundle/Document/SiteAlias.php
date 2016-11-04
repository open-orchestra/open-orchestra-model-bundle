<?php

namespace OpenOrchestra\ModelBundle\Document;

use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\MongoTrait\Schemeable;
use OpenOrchestra\MongoTrait\Metaable;

/**
 * Class SiteAlias
 *
 * @ODM\EmbeddedDocument
 */
class SiteAlias implements SiteAliasInterface
{
    use Schemeable;
    use Metaable;

    /**
     * @var string $domain
     *
     * @ODM\Field(type="string")
     */
    protected $domain;

    /**
     * @var string $language
     *
     * @ODM\Field(type="string")
     */
    protected $language;

    /**
     * @var string $prefix
     *
     * @ODM\Field(type="string")
     */
    protected $prefix;

    /**
     * @var boolean $main
     *
     * @ODM\Field(type="boolean")
     */
    protected $main;

    /**
     * @var string $metaDescription
     *
     * @ODM\Field(type="string")
     */
    protected $metaDescription;

    /**
     * @var string $googleMarker
     *
     * @ODM\Field(type="string")
     */
    protected $googleMarker;

    /**
     * @var boolean $cnilCompliance
     *
     * @ODM\Field(type="boolean")
     */
    protected $cnilCompliance;

    /**
     * @var string $xtsd
     *
     * @ODM\Field(type="string")
     */
    protected $xtsd;

    /**
     * @var string $xtside
     *
     * @ODM\Field(type="string")
     */
    protected $xtside;

    /**
     * @var string $xtn2
     *
     * @ODM\Field(type="string")
     */
    protected $xtn2;

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return boolean
     */
    public function isMain()
    {
        return $this->main;
    }

    /**
     * @param bool $main
     */
    public function setMain($main)
    {
        $this->main = $main;
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
     * @param string $googleMarker
     */
    public function setGoogleMarker($googleMarker)
    {
        $this->googleMarker = $googleMarker;
    }

    /**
     * @return string
     */
    public function getGoogleMarker()
    {
        return $this->googleMarker;
    }

    /**
     * @param bool $cnilCompliance
     */
    public function setCnilCompliance($cnilCompliance)
    {
        $this->cnilCompliance = $cnilCompliance;
    }

    /**
     * @return bool
     */
    public function isCnilCompliance()
    {
        return $this->cnilCompliance;
    }

    /**
     * @param string $xtsd
     */
    public function setXtsd($xtsd)
    {
        $this->xtsd = $xtsd;
    }

    /**
     * @return string
     */
    public function getXtsd()
    {
        return $this->xtsd;
    }

    /**
     * @param string $xtside
     */
    public function setXtside($xtside)
    {
        $this->xtside = $xtside;
    }

    /**
     * @return string
     */
    public function getXtside()
    {
        return $this->xtside;
    }

    /**
     * @param string $xtn2
     */
    public function setXtn2($xtn2)
    {
        $this->xtn2 = $xtn2;
    }

    /**
     * @return string
     */
    public function getXtn2()
    {
        return $this->xtn2;
    }
}

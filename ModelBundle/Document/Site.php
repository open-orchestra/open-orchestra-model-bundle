<?php

namespace PHPOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use PHPOrchestra\ModelInterface\Model\SiteInterface;
use PHPOrchestra\ModelInterface\Model\ThemeInterface;
use PHPOrchestra\ModelInterface\MongoTrait\MetaableDocument;

/**
 * Description of Site
 *
 * @ODM\Document(
 *   collection="site",
 *   repositoryClass="PHPOrchestra\ModelBundle\Repository\SiteRepository"
 * )
 */
class Site implements SiteInterface
{
    use MetaableDocument;

    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $siteId
     *
     * @ODM\Field(type="string")
     */
    protected $siteId;

    /**
     * @var string $domain
     *
     * @ODM\Field(type="string")
     */
    protected $domain;

    /**
     * @var string $alias
     *
     * @ODM\Field(type="string")
     */
    protected $alias;

    /**
     * @var string $defaultLanguage
     *
     * @ODM\Field(type="string")
     */
    protected $defaultLanguage;

    /**
     * @var array $languages
     *
     * @ODM\Field(type="collection")
     */
    protected $languages = array();

    /**
     * @var boolean
     *
     * @ODM\Field(type="boolean")
     */
    protected $deleted = false;

    /**
     * @var ArrayCollection
     *
     * @ODM\Field(type="hash")
     */
    protected $blocks = array();

    /**
     * @var ThemeInterface $theme
     *
     * @ODM\ReferenceOne(targetDocument="Theme")
     */
    protected $theme;

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $block
     */
    public function addBlock($block)
    {
        $this->blocks[] = $block;
    }

    /**
     * @param string $block
     */
    public function removeBlock($block)
    {
        null;
    }

    /**
     * @return array
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param string $defaultLanguage
     */
    public function setDefaultLanguage($defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
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
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param array $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
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
     * Set theme
     *
     * @param ThemeInterface $theme
     */
    public function setTheme(ThemeInterface $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Get theme
     *
     * @return ThemeInterface $theme
     */
    public function getTheme()
    {
        return $this->theme;
    }
}

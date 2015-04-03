<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\ReadSiteAliasInterface;
use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
use OpenOrchestra\ModelInterface\Mapping\Annotations as ORCHESTRA;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Model\ThemeInterface;
use OpenOrchestra\ModelInterface\MongoTrait\Metaable;
use OpenOrchestra\ModelInterface\MongoTrait\Sitemapable;

/**
 * Description of Site
 *
 * @ODM\Document(
 *   collection="site",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\SiteRepository"
 * )
 * @ORCHESTRA\Document(
 *   generatedField="siteId",
 *   sourceField="name",
 *   serviceName="open_orchestra_model.repository.site",
 * )
 */
class Site implements SiteInterface
{
    use Metaable;
    use Sitemapable;

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
     * @var string $robotsTxt
     *
     * @ODM\Field(type="string")
     */
    protected $robotsTxt;

    /**
     * @var string $name
     *
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var Collection
     *
     * @ODM\EmbedMany(targetDocument="SiteAlias")
     */
    protected $aliases;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->robotsTxt = SiteInterface::ROBOTS_TXT_DEFAULT;
        $this->aliases = new ArrayCollection();
    }

    /**
     * @param SiteAliasInterface $alias
     */
    public function addAlias(SiteAliasInterface $alias)
    {
        $this->aliases->add($alias);
    }

    /**
     * @param SiteAliasInterface $alias
     */
    public function removeAlias(SiteAliasInterface $alias)
    {
        $this->aliases->removeElement($alias);
    }

    /**
     * @return Collection
     */
    public function getAliases()
    {
        return $this->aliases;
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
        $newBlocks = array();
        foreach ($this->blocks as $blockSite) {
            if ($blockSite !== $block) {
                $newBlocks[] = $blockSite;
            }
        }
        $this->blocks = $newBlocks;
    }

    /**
     * @return array
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * Set robotsTxt
     *
     * @param string $robotsTxt
     */
    public function setRobotsTxt($robotsTxt)
    {
        $this->robotsTxt = $robotsTxt;
    }

    /**
     * Get robotsTxt
     *
     * @return string $robotsTxt
     */
    public function getRobotsTxt()
    {
        return $this->robotsTxt;
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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get all languages of the site
     *
     * @return array
     */
    public function getLanguages()
    {
        $languages = array();

        /** @var SiteAliasInterface $siteAlias */
        foreach ($this->getAliases() as $siteAlias) {
            $language = $siteAlias->getLanguage();
            if (!in_array($language, $languages)) {
                $languages[] = $language;
            }
        }

        return $languages;
    }

    /**
     * Return one of the defailt site language
     *
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->getMainAlias()->getLanguage();
    }

    /**
     * @return ReadSiteAliasInterface
     */
    public function getMainAlias()
    {
        /** @var SiteAliasInterface $alias */
        foreach ($this->getAliases() as $alias) {
            if ($alias->isMain()) {
                return $alias;
            }
        }

        return $alias;
    }
}

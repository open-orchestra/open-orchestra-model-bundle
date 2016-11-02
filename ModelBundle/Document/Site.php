<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\ReadSiteAliasInterface;
use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Model\ThemeInterface;
use OpenOrchestra\MongoTrait\Metaable;
use OpenOrchestra\MongoTrait\Sitemapable;
use OpenOrchestra\MongoTrait\SoftDeleteable;
use OpenOrchestra\ModelInterface\Exceptions\MainAliasNotExisting;

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
    use Sitemapable;
    use SoftDeleteable;

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
     * @ORCHESTRA\Search(key="site_id")
     */
    protected $siteId;

    /**
     * @var ArrayCollection
     *
     * @ODM\Field(type="hash")
     */
    protected $blocks = array();

    /**
     * @var ThemeInterface $theme
     *
     * @ODM\ReferenceOne(targetDocument="OpenOrchestra\ModelInterface\Model\ThemeInterface")
     */
    protected $theme;

    /**
     * @var string $metaAuthor
     *
     * @ODM\Field(type="string")
     */
    protected $metaAuthor;

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
     * @ORCHESTRA\Search(key="name")
     */
    protected $name;

    /**
     * @var string $templateSet
     *
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="template_set")
     */
    protected $templateSet;

    /**
     * @var string $templateNodeRoot
     *
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="template_root")
     */
    protected $templateNodeRoot;

    /**
     * @var Collection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\SiteAliasInterface", strategy="set")
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
        $this->aliases->set(uniqid(SiteInterface::PREFIX_SITE_ALIAS), $alias);
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
     * Set metaAuthor
     *
     * @param string $metaAuthor
     */
    public function setMetaAuthor($metaAuthor)
    {
        $this->metaAuthor = $metaAuthor;
    }

    /**
     * Get metaAuthor
     *
     * @return string $metaAuthor
     */
    public function getMetaAuthor()
    {
        return $this->metaAuthor;
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
     * Get templateSet
     *
     * @return string $templateSet
     */
    public function getTemplateSet()
    {
        return $this->templateSet;
    }

    /**
     * Set templateSet
     *
     * @param string $templateSet
     */
    public function setTemplateSet($templateSet)
    {
        $this->templateSet = $templateSet;
    }

    /**
     * Get templateNodeRoot
     *
     * @return string $templateNodeRoot
     */
    public function getTemplateNodeRoot()
    {
        return $this->templateNodeRoot;
    }

    /**
     * Set templateNodeRoot
     *
     * @param string $templateNodeRoot
     */
    public function setTemplateNodeRoot($templateNodeRoot)
    {
        $this->templateNodeRoot = $templateNodeRoot;
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
     * Return one of the default site language
     *
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->getMainAlias()->getLanguage();
    }

    /**
     * @return ReadSiteAliasInterface
     *
     * @throws MainAliasNotExisting
     */
    public function getMainAlias()
    {
        /** @var SiteAliasInterface $alias */
        foreach ($this->getAliases() as $alias) {
            if ($alias->isMain()) {
                return $alias;
            }
        }

        throw new MainAliasNotExisting();
    }

    /**
     * return int
     */
    public function getMainAliasId()
    {
        return $this->aliases->indexOf($this->getMainAlias());
    }

    /**
     * Return alias id for given language
     *
     * @param string $language
     *
     * @return int
     */
    public function getAliasIdForLanguage($language)
    {
        /** @var ReadSiteAliasInterface $alias */
        foreach ($this->aliases as $key => $alias) {
            if ($alias->getLanguage() == $language) {
                return $key;
            }
        }

        return null;
    }
}

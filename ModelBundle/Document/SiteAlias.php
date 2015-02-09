<?php

namespace PHPOrchestra\ModelBundle\Document;

use PHPOrchestra\ModelInterface\Model\SiteAliasInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SiteAlias
 *
 * @ODM\EmbeddedDocument
 */
class SiteAlias implements SiteAliasInterface
{
    /**
     * @var string $domain
     *
     * @ODM\Field(type="string")
     */
    protected $domain;

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
}

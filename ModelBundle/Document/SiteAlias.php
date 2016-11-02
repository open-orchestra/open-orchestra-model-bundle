<?php

namespace OpenOrchestra\ModelBundle\Document;

use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\MongoTrait\Schemeable;

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
}

<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\MongoTrait\Cacheable;

/**
 * Class Block
 *
 * @ODM\Document(
 *   collection="block",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\BlockRepository"
 * )
 */
class Block implements BlockInterface
{
    use Cacheable;
    use TimestampableDocument;

    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $component
     *
     * @ODM\Field(type="string")
     */
    protected $component;

    /**
     * @var boolean $transverse
     *
     * @ODM\Field(type="boolean")
     */
    protected $transverse = false;

    /**
     * @var string $label
     *
     * @ODM\Field(type="string")
     */
    protected $label;

    /**
     * @var string $style
     *
     * @ODM\Field(type="string")
     */
    protected $style;

    /**
     * @var string $language
     *
     * @ODM\Field(type="string")
     */
    protected $language;

    /**
     * @var string $siteId
     *
     * @ODM\Field(type="string")
     */
    protected $siteId;

    /**
     * @var string $siteId
     *
     * @ODM\Field(type="string")
     */
    protected $code;

    /**
     * @var array $attributes
     *
     * @ODM\Field(type="hash")
     */
    protected $attributes = array();

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
     * Set id
     *
     * @param string $id
     */
    public function setId($id)
    {
        if (null ===  $this->id) {
            $this->id = $id;
        }
    }

    /**
     * Set component
     *
     * @param string $component
     */
    public function setComponent($component)
    {
        $this->component = $component;
    }

    /**
     * Get component
     *
     * @return string $component
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * Set transverse
     *
     * @param boolean $transverse
     */
    public function setTransverse($transverse)
    {
        $this->transverse = $transverse;
    }

    /**
     * Get transverse
     *
     * @return boolean
     */
    public function isTransverse()
    {
        return $this->transverse;
    }

    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string $label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set attributes
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function addAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getAttribute($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        return null;
    }

    /**
     * Set style
     *
     * @param string $style
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }

    /**
     * Get style
     *
     * @return string $style
     */
    public function getStyle()
    {
        return $this->style;
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
     * Set site id
     *
     * @param string $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * Get site id
     *
     * @return string $siteId
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * Get Code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set Code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Clone method
     */
    public function __clone()
    {
        if (!is_null($this->id)) {
            $this->id = null;
        }
    }
}

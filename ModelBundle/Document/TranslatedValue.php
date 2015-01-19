<?php

namespace PHPOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use PHPOrchestra\ModelInterface\Model\TranslatedValueInterface;

/**
 * Class TranslatedValue
 *
 * @ODM\EmbeddedDocument
 */
class TranslatedValue implements TranslatedValueInterface
{
    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $language;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $value;

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
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}

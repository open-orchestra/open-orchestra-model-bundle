<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\TranslatedValueInterface;

@trigger_error('The '.__NAMESPACE__.'\EmbedKeyword class is deprecated since version 1.2.0 and will be removed in 2.0', E_USER_DEPRECATED);

/**
 * Class TranslatedValue
 *
 * @deprecated will be removed in 2.0
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

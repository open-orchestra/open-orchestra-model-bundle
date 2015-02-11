<?php

namespace PHPOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use PHPOrchestra\ModelInterface\Model\FieldOptionInterface;
use PHPOrchestra\ModelInterface\Model\FieldTypeInterface;
use PHPOrchestra\ModelInterface\Model\TranslatedValueInterface;

/**
 * Description of Base FieldType
 *
 * @ODM\EmbeddedDocument
 */
class FieldType implements FieldTypeInterface
{
    /**
     * @var string $fieldId
     *
     * @ODM\Field(type="string")
     */
    protected $fieldId;

    /**
     * @var ArrayCollection $labels
     *
     * @ODM\EmbedMany(targetDocument="TranslatedValue")
     */
    protected $labels;

    /**
     * @var string $defaultValue
     *
     * @ODM\Field(type="string")
     */
    protected $defaultValue;

    /**
     * @var boolean $searchable
     *
     * @ODM\Field(type="boolean")
     */
    protected $searchable;

    /**
     * @var string $type
     *
     * @ODM\Field(type="string")
     */
    protected $type;

    /**
     * @var string $symfonyType
     *
     * @ODM\Field(type="string")
     */
    protected $symfonyType;

    /**
     * @var ArrayCollection $options
     *
     * @ODM\EmbedMany(targetDocument="FieldOption")
     */
    protected $options;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->labels = new ArrayCollection();
    }
    /**
     * Set FieldId
     *
     * @param string $fieldId
     */
    public function setFieldId($fieldId)
    {
        $this->fieldId = $fieldId;
    }

    /**
     * Get FieldId
     *
     * @return string
     */
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * @param TranslatedValueInterface $label
     */
    public function addLabel(TranslatedValueInterface $label)
    {
        $this->labels->add($label);
    }

    /**
     * @param TranslatedValueInterface $label
     */
    public function removeLabel(TranslatedValueInterface $label)
    {
        $this->labels->removeElement($label);
    }

    /**
     * @return ArrayCollection
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param string $language
     *
     * @return mixed
     */
    public function getLabel($language = 'en')
    {
        $choosenLanguage = $this->labels->filter(function (TranslatedValueInterface $translatedValue) use ($language) {
            return $language == $translatedValue->getLanguage();
        });

        return $choosenLanguage->first()->getValue();
    }

    /**
     * Set Default Value
     *
     * @param string $value
     */
    public function setDefaultValue($value)
    {
        $this->defaultValue = $value;
    }

    /**
     * Get Default Value
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set Searchable
     *
     * @param boolean $searchable
     */
    public function setSearchable($searchable)
    {
        $this->searchable = $searchable;
    }

    /**
     * @return boolean
     */
    public function getSearchable()
    {
        return $this->searchable;
    }

    /**
     * Set Type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param FieldOptionInterface $option
     */
    public function addOption(FieldOptionInterface $option)
    {
        $this->options->add($option);
    }

    /**
     * @param FieldOptionInterface $option
     */
    public function removeOption(FieldOptionInterface $option)
    {
        $this->options->removeElement($option);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasOption($key)
    {
        return $this->options->filter(function(FieldOptionInterface $option) use ($key) {
            return $option->getKey() == $key;
        })->count();
    }

    /**
     * @return ArrayCollection
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getFormOptions()
    {
        $formOptions = array();

        foreach ($this->getOptions() as $option) {
            $formOptions[Inflector::tableize($option->getKey())] = $option->getValue();
        }

        return $formOptions;
    }

    /**
     * @return array
     */
    public function getTranslatedProperties()
    {
        return array(
            'getLabels'
        );
    }

    /**
     * Clone the element
     */
    public function __clone()
    {
        $this->labels = new ArrayCollection();
    }
}

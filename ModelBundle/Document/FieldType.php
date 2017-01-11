<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\FieldOptionInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;

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
     * @var array $labels
     *
     * @ODM\Field(type="hash")
     */
    protected $labels;

    /**
     * @var string $defaultValue
     *
     * @ODM\Field(type="raw")
     */
    protected $defaultValue;

    /**
     * @var boolean $searchable
     *
     * @ODM\Field(type="boolean")
     */
    protected $searchable;

    /**
     * @var boolean $orderable
     *
     * @ODM\Field(type="boolean")
     */
    protected $orderable;

    /**
     * @var string $fieldTypeSearchable
     *
     * @ODM\Field(type="string")
     */
    protected $fieldTypeSearchable;

    /**
     * @var boolean $translatable
     *
     * @ODM\Field(type="boolean")
     */
    protected $translatable = true;

    /**
     * @var boolean $listable
     *
     * @ODM\Field(type="boolean")
     */
    protected $listable;

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
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\FieldOptionInterface")
     */
    protected $options;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->labels = array();
        $this->initializeCollection();
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
     * @param string $language
     * @param string $label
     */
    public function addLabel($language, $label)
    {
        if (is_string($language) && is_string($label)) {
            $this->labels[$language] = $label;
        }
    }

    /**
     * @param string $language
     */
    public function removeLabel($language)
    {
        if (is_string($language) && isset($this->labels[$language])) {
            unset($this->labels[$language]);
        }
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getLabel($language)
    {
        if (isset($this->labels[$language])) {
            return $this->labels[$language];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param array $labels
     */
    public function setLabels(array $labels)
    {
        foreach ($labels as $language => $label) {
            $this->addLabel($language, $label);
        }
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
     * @return boolean
     */
    public function getListable()
    {
        return $this->listable;
    }

    /**
     * Set Searchable
     *
     * @param boolean $listable
     */
    public function setListable($listable)
    {
        $this->listable = $listable;
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
    public function isSearchable()
    {
        return $this->searchable;
    }

    /**
     * Set Orderable
     *
     * @param boolean $orderable
     */
    public function setOrderable($orderable)
    {
        $this->orderable = $orderable;
    }

    /**
     * @return boolean
     */
    public function isOrderable()
    {
        return $this->orderable;
    }

    /**
     * @return string
     */
    public function getFieldTypeSearchable()
    {
        return $this->fieldTypeSearchable;
    }

    /**
     * Set field searchable
     *
     * @param string $fieldTypeSearchable
     */
    public function setFieldTypeSearchable($fieldTypeSearchable)
    {
        $this->fieldTypeSearchable = $fieldTypeSearchable;
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
        })->count() != 0;
    }

    /**
     * @return ArrayCollection
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set Options
     */
    public function clearOptions()
    {
        $this->options = new ArrayCollection();
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
     * Clone the element
     */
    public function __clone()
    {
        $this->initializeCollection();
    }

    /**
     * Initialize collection
     */
    protected function initializeCollection()
    {
        $this->options = new ArrayCollection();
    }

    /**
     * @return boolean
     */
    public function isTranslatable()
    {
        return $this->translatable;
    }

    /**
     * @param boolean $translatable
     */
    public function setTranslatable($translatable)
    {
        $this->translatable = $translatable;
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\FieldOptionInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use OpenOrchestra\ModelInterface\Model\TranslatedValueInterface;
use OpenOrchestra\ModelInterface\ModelTrait\TranslatedValueFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of Base FieldType
 *
 * @ODM\EmbeddedDocument
 */
class FieldType implements FieldTypeInterface
{
    use TranslatedValueFilter;

    /**
     * @var string $fieldId
     *
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9_][a-zA-Z0-9_\-:]*$/D",
     *     message="open_orchestra_model_validators.field.special_character"
     * )
     * @Assert\NotBlank()
     * @ODM\Field(type="string")
     */
    protected $fieldId;

    /**
     * @var ArrayCollection $labels
     *
     * @Assert\Valid
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\TranslatedValueInterface")
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
     * @Assert\Type(type="bool")
     * @ODM\Field(type="boolean")
     */
    protected $searchable;

    /**
     * @var boolean $translatable
     *
     * @Assert\Type(type="bool")
     * @ODM\Field(type="boolean")
     */
    protected $translatable = true;

    /**
     * @var boolean $listable
     *
     * @Assert\Type(type="bool")
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
     * @Assert\Valid
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\FieldOptionInterface")
     */
    protected $options;

    /**
     * Constructor
     */
    public function __construct()
    {
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
        return $this->filterByLanguage($this->labels, $language);
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
        $this->initializeCollection();
    }

    /**
     * Initialize collection
     */
    protected function initializeCollection()
    {
        $this->labels = new ArrayCollection();
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

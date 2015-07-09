<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;

/**
 * Description of ContentAttribute
 *
 * @ODM\EmbeddedDocument
 */
class ContentAttribute implements ContentAttributeInterface
{
    /**
     * @var string $name
     *
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var mixed $value
     *
     * @ODM\Field(type="raw")
     */
    protected $value;

    /**
     * @var string $stringValue
     *
     * @ODM\Field(type="string")
     */
    protected $stringValue;

    /**
     * @var string $type
     *
     * @ODM\Field(type="string")
     */
    protected $type;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $stringValue
     */
    public function setStringValue($stringValue)
    {
        $this->stringValue = $stringValue;
    }

    /**
     * @return string
     */
    public function getStringValue()
    {
        return  (string)  $this->stringValue;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return  $this->type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getStringValue();
    }
}

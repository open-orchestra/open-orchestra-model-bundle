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
     * @return string
     */
    public function renderValue()
    {
        if (is_array($this->value) || is_object($this->value)) {
            return "Complex Object (no displayable)";
        }

        return (string) $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->renderValue();
    }
}

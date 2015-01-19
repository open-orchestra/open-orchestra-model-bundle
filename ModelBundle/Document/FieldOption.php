<?php

namespace PHPOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use PHPOrchestra\ModelInterface\Model\FieldOptionInterface;

/**
 * Description of FieldOption
 *
 * @ODM\EmbeddedDocument
 */
class FieldOption implements FieldOptionInterface
{
    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $key;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $value;

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = serialize($value);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return unserialize($this->value);
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\FieldIndexInterface;

/**
 * Description of FieldIndex class
 *
 * @ODM\Document(
 *   collection="field_index",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\FieldIndexRepository"
 * )
 */
class FieldIndex implements FieldIndexInterface
{
    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $fieldName
     *
     * @ODM\Field(type="string")
     */
    protected $fieldName;

    /**
     * @var string $fieldType
     *
     * @ODM\Field(type="string")
     */
    protected $fieldType;

    /**
     * @var boolean
     *
     * @ODM\Field(type="boolean")
     */
    protected $link;

    /**
     * @param string $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldType
     */
    public function setFieldType($fieldType)
    {
        $this->fieldType = $fieldType;
    }

    /**
     * @return string
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * @param boolean $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return boolean
     */
    public function isLink()
    {
        return $this->link;
    }
}

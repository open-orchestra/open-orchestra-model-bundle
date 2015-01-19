<?php

namespace PHPOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use PHPOrchestra\ModelInterface\Model\FieldIndexInterface;

/**
 * Description of FieldIndex class
 *
 * @ODM\Document(
 *   collection="field_index",
 *   repositoryClass="PHPOrchestra\ModelBundle\Repository\FieldIndexRepository"
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
    protected $isLink;

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
     * @param boolean $isLink
     */
    public function setIsLink($isLink)
    {
        $this->isLink = $isLink;
    }

    /**
     * @return boolean
     */
    public function getIsLink()
    {
        return $this->isLink;
    }
}

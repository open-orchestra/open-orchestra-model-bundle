<?php

namespace PHPOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use PHPOrchestra\ModelBundle\Model\FieldIndexInterface;

/**
 * Description of FieldIndex class
 *
 * @MongoDB\Document(
 *   collection="field_index",
 *   repositoryClass="PHPOrchestra\ModelBundle\Repository\FieldIndexRepository"
 * )
 */
class FieldIndex implements FieldIndexInterface
{
    /**
     * @var string $id
     *
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var string $fieldName
     *
     * @MongoDB\Field(type="string")
     */
    protected $fieldName;

    /**
     * @var string $fieldType
     *
     * @MongoDB\Field(type="string")
     */
    protected $fieldType;

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
}

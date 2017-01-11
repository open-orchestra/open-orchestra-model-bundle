<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\MongoTrait\SoftDeleteable;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\MongoTrait\SiteLinkable;
use OpenOrchestra\MongoTrait\Versionable;

/**
 * Description of ContentType
 *
 * @ODM\Document(
 *   collection="content_type",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\ContentTypeRepository"
 * )
 */
class ContentType implements ContentTypeInterface
{
    use BlameableDocument;
    use TimestampableDocument;
    use Versionable;
    use SiteLinkable;
    use SoftDeleteable;

    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $contentTypeId
     *
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="content_type_id")
     */
    protected $contentTypeId;

    /**
     * @ODM\Field(type="hash")
     * @ORCHESTRA\Search(key="name", type="multiLanguages")
     */
    protected $names;

    /**
     * @var ArrayCollection $defaultListable
     *
     * @ODM\Field(type="hash")
     */
    protected $defaultListable;

    /**
     * @var string $template
     *
     * @ODM\Field(type="string")
     */
    protected $template;

    /**
     * @var ArrayCollection $fields
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\FieldTypeInterface")
     */
    protected $fields;

    /**
     * @var boolean definingVersionable
     *
     * @ODM\Field(type="boolean")
     */
    protected $definingVersionable = true;

    /**
     * @var boolean definingStatusable
     *
     * @ODM\Field(type="boolean")
     */
    protected $definingStatusable = true;

    /**
     * @var boolean alwaysShared
     *
     * @ODM\Field(type="boolean")
     */
    protected $alwaysShared = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->names = array();
        $this->defaultListable = array();
    }

    /**
     * @param string $contentTypeId
     */
    public function setContentTypeId($contentTypeId)
    {
        $this->contentTypeId = $contentTypeId;
    }

    /**
     * @return string
     */
    public function getContentTypeId()
    {
        return $this->contentTypeId;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param FieldTypeInterface $field
     */
    public function addFieldType(FieldTypeInterface $field)
    {
        $this->fields->add($field);
    }

    /**
     * @param Collection $fields
     */
    public function setFields(Collection $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param FieldTypeInterface $field
     */
    public function removeFieldType(FieldTypeInterface $field)
    {
        $this->fields->removeElement($field);
    }

    /**
     * @return ArrayCollection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $language
     * @param string $name
     */
    public function addName($language, $name)
    {
        if (is_string($language) && is_string($name)) {
            $this->names[$language] = $name;
        }
    }

    /**
     * @param string $language
     */
    public function removeName($language)
    {
        if (is_string($language) && isset($this->names[$language])) {
            unset($this->names[$language]);
        }
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getName($language)
    {
        if (isset($this->names[$language])) {
            return $this->names[$language];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @param array $names
     */
    public function setNames(array $names)
    {
        foreach ($names as $language => $name) {
            $this->addName($language, $name);
        }
    }

    /**
     * @return array
     */
    public function getDefaultListable()
    {
        return $this->defaultListable;
    }

    /**
     * @param string  $name
     * @param boolean $value
     */
    public function addDefaultListable($name, $value)
    {
        $this->defaultListable[$name] = $value;
    }

    /**
     * @param string $name
     */
    public function removeDefaultListable($name)
    {
        $this->defaultListable->removeElement($name);
    }

    /**
     * @param array $defaultListable
     */
    public function setDefaultListable(array $defaultListable)
    {
        $this->defaultListable = $defaultListable;
    }

    /**
     * @param boolean $definingVersionable
     */
    public function setDefiningVersionable($definingVersionable)
    {
        $this->definingVersionable = $definingVersionable;
    }

    /**
     * @return boolean
     */
    public function isDefiningVersionable()
    {
        return $this->definingVersionable;
    }

    /**
     * @param boolean $definingStatusable
     */
    public function setDefiningStatusable($definingStatusable)
    {
        $this->definingStatusable = $definingStatusable;
    }

    /**
     * @return boolean
     */
    public function isDefiningStatusable()
    {
        return $this->definingStatusable;
    }

    /**
     * @param boolean $alwaysShared
     */
    public function setAlwaysShared($alwaysShared)
    {
        $this->alwaysShared = $alwaysShared;
    }

    /**
     * @return boolean
     */
    public function isAlwaysShared()
    {
        return $this->alwaysShared;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getContentTypeId();
    }

    /**
     * Clone the element
     */
    public function __clone()
    {
        if (!is_null($this->id)) {
            $this->id = null;
            $this->fields = new ArrayCollection();
            $this->setUpdatedAt(new \DateTime());
            $this->setVersion($this->getVersion() + 1);
        }
    }
}

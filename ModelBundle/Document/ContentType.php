<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use Gedmo\Mapping\Annotation as Gedmo;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\Model\TranslatedValueInterface;
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
     */
    protected $contentTypeId;

    /**
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\TranslatedValueInterface", strategy="set")
     */
    protected $names;

    /**
     * @var boolean $deleted
     *
     * @ODM\Field(type="boolean")
     */
    protected $deleted = false;

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
     * Constructor
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->names = new ArrayCollection();
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
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
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
     * @param FieldTypeInterface $fields
     */
    public function setFields(FieldTypeInterface $fields)
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
     * @param TranslatedValueInterface $name
     */
    public function addName(TranslatedValueInterface $name)
    {
        $this->names->set($name->getLanguage(), $name);
    }

    /**
     * @param TranslatedValueInterface $name
     */
    public function removeName(TranslatedValueInterface $name)
    {
        $this->names->remove($name->getLanguage());
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getName($language = 'en')
    {
        return $this->names->get($language)->getValue();
    }

    /**
     * @return ArrayCollection
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getContentTypeId();
    }

    /**
     * @return array
     */
    public function getTranslatedProperties()
    {
        return array(
            'getNames'
        );
    }

    /**
     * Clone the element
     */
    public function __clone()
    {
        if (!is_null($this->id)) {
            $this->id = null;
            $this->names = new ArrayCollection();
            $this->fields = new ArrayCollection();
            $this->setUpdatedAt(new \DateTime());
            $this->setVersion($this->getVersion() + 1);
        }
    }
}

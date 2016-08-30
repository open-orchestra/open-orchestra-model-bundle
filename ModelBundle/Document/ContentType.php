<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Exceptions\TranslatedValueNotExisting;
use OpenOrchestra\MongoTrait\SoftDeleteable;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\Model\TranslatedValueInterface;
use OpenOrchestra\MongoTrait\SiteLinkable;
use OpenOrchestra\MongoTrait\Versionable;
use OpenOrchestra\MongoTrait\IsStatusable;

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
    use IsStatusable;

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
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\TranslatedValueInterface", strategy="set")
     * @ORCHESTRA\Search(key="name", type="translatedValue")
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
     * Constructor
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->names = new ArrayCollection();
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
     * @param string $language
     *
     * @return string
     * @throws TranslatedValueNotExisting
     */
    public function getName($language)
    {
        if ($this->names->containsKey($language)) {
            return $this->names->get($language)->getValue();
        }

        throw new TranslatedValueNotExisting();
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

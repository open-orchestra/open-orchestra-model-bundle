<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use OpenOrchestra\MongoTrait\SiteLinkable;
use OpenOrchestra\MongoTrait\SoftDeleteable;
use OpenOrchestra\MongoTrait\Statusable;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Model\ReadContentAttributeInterface;
use OpenOrchestra\MongoTrait\Keywordable;
use OpenOrchestra\MongoTrait\Versionable;
use OpenOrchestra\MongoTrait\UseTrackable;
use OpenOrchestra\MongoTrait\Historisable;
use OpenOrchestra\MongoTrait\AutoPublishable;

/**
 * Description of Content
 *
 * @ODM\Document(
 *   collection="content",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\ContentRepository"
 * )
 * @ODM\Indexes({
 *  @ODM\Index(keys={"contentId"="asc"}),
 *  @ODM\Index(keys={"language"="asc", "deleted"="asc", "status.publishedState"="asc", "contentType"="asc", "keywords.label"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"language"="asc", "deleted"="asc", "status.publishedState"="asc", "keywords.label"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"language"="asc", "deleted"="asc", "status.publishedState"="asc", "contentType"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"language"="asc", "deleted"="asc", "status.publishedState"="asc", "version"="desc"}),
 *  @ODM\Index(keys={"keywords"="asc"})
 * })
 * @ORCHESTRA\Document(
 *   generatedField="contentId",
 *   sourceField="name",
 *   serviceName="open_orchestra_model.repository.content",
 * )
 */
class Content implements ContentInterface
{
    use BlameableDocument;
    use TimestampableDocument;
    use Keywordable;
    use Statusable;
    use Versionable;
    use SiteLinkable;
    use SoftDeleteable;
    use UseTrackable;
    use Historisable;
    use AutoPublishable;

    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var int $contentId
     *
     * @ODM\Field(type="string")
     */
    protected $contentId;

    /**
     * @var string $contentType
     *
     * @ODM\Field(type="string")
     */
    protected $contentType;

    /**
     * @var string $name
     *
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var string $language
     *
     * @ODM\Field(type="string")
     */
    protected $language;

    /**
     * @var ArrayCollection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\ContentAttributeInterface", strategy="set")
     */
    protected $attributes;

    /**
     * @var string $siteId
     *
     * @ODM\Field(type="string")
     */
    protected $siteId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initializeCollections();
    }

    /**
     * @return ArrayCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     *
     * @return ReadContentAttributeInterface|null
     */
    public function getAttributeByName($name)
    {
        return $this->attributes->get($name);
    }

    /**
     * @param ContentAttributeInterface $attribute
     */
    public function addAttribute(ContentAttributeInterface $attribute)
    {
        $this->attributes->set($attribute->getName(), $attribute);
    }

    /**
     * @param ContentAttributeInterface $attribute
     */
    public function removeAttribute(ContentAttributeInterface $attribute)
    {
        $this->attributes->remove($attribute->getName());
    }

    /**
     * @param string $contentId
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;
    }

    /**
     * @return string
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
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
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

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
     * @return string
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param string $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * Clone method
     */
    public function __clone()
    {
        $this->id = null;
        $this->useReferences = array();
        $this->initializeCollections();
    }

    /**
     * initialize collections
     */
    protected function initializeCollections()
    {
        $this->attributes = new ArrayCollection();
        $this->keywords = new ArrayCollection();
        $this->initializeHistories();
    }
}

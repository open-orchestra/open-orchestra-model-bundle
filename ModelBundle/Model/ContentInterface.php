<?php


namespace PHPOrchestra\ModelBundle\Model;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Interface ContentInterface
 */
interface ContentInterface
{
    /**
     * @return ArrayCollection
     */
    public function getAttributes();

    /**
     * @param ContentAttributeInterface $attribute
     */
    public function addAttribute(ContentAttributeInterface $attribute);

    /**
     * @param ContentAttributeInterface $attribute
     */
    public function removeAttribute(ContentAttributeInterface $attribute);

    /**
     * @param string $contentId
     */
    public function setContentId($contentId);

    /**
     * @return string
     */
    public function getContentId();

    /**
     * @param string $contentType
     */
    public function setContentType($contentType);

    /**
     * @return string
     */
    public function getContentType();

    /**
     * @param int $contentTypeVersion
     */
    public function setContentTypeVersion($contentTypeVersion);

    /**
     * @return int
     */
    public function getContentTypeVersion();

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted);

    /**
     * @return boolean
     */
    public function getDeleted();

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $language
     */
    public function setLanguage($language);

    /**
     * @return string
     */
    public function getLanguage();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param int $siteId
     */
    public function setSiteId($siteId);

    /**
     * @return int
     */
    public function getSiteId();

    /**
     * @param string $status
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param int $version
     */
    public function setVersion($version);

    /**
     * @return int
     */
    public function getVersion();
}

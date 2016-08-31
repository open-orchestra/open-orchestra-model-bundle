<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;

/**
 * Class Role
 *
 * @ODM\Document(
 *   collection="role",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\RoleRepository"
 * )
 */
class Role implements RoleInterface
{
    /**
     * @ODM\Id()
     */
    protected $id;

    /**
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var StatusInterface
     *
     * @ODM\ReferenceOne(targetDocument="OpenOrchestra\ModelInterface\Model\StatusInterface", inversedBy="fromRoles")
     * @ORCHESTRA\Search(key="from_status", field="fromStatus.label", type="reference")
     */
    protected $fromStatus;

    /**
     * @var StatusInterface
     *
     * @ODM\ReferenceOne(targetDocument="OpenOrchestra\ModelInterface\Model\StatusInterface", inversedBy="toRoles")
     * @ORCHESTRA\Search(key="to_status", field="toStatus.label", type="reference")
     */
    protected $toStatus;

    /**
     * @ODM\Field(type="hash")
     * @ORCHESTRA\Search(key="description", type="multiLanguages")
     */
    protected $descriptions;

    /**
     * Construct the class
     */
    public function __construct()
    {
        $this->descriptions = array();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return StatusInterface
     */
    public function getToStatus()
    {
        return $this->toStatus;
    }

    /**
     * @param StatusInterface $toStatus
     */
    public function setToStatus(StatusInterface $toStatus)
    {
        $this->toStatus = $toStatus;
        $toStatus->addToRole($this);
    }

    /**
     * @return StatusInterface
     */
    public function getFromStatus()
    {
        return $this->fromStatus;
    }

    /**
     * @param StatusInterface $fromStatus
     */
    public function setFromStatus(StatusInterface $fromStatus)
    {
        $this->fromStatus = $fromStatus;
        $fromStatus->addFromRole($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param string $language
     * @param string $description
     */
    public function addDescription($language, $description)
    {
        if (is_string($language) && is_string($description)) {
            $this->descriptions[$language] = $description;
        }
    }

    /**
     * @param string $language
     */
    public function removeDescription($language)
    {
        if (is_string($language) && isset($this->descriptions[$language])) {
            unset($this->descriptions[$language]);
        }
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getDescription($language)
    {
        if (isset($this->descriptions[$language])) {
            return $this->descriptions[$language];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

    /**
     * @param array $descriptions
     */
    public function setDescriptions(array $descriptions)
    {
        foreach ($descriptions as $language => $description) {
            $this->addDescription($language, $description);
        }
    }
}

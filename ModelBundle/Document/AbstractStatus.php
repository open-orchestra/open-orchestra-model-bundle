<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;

/**
 * Class AbstractStatus
 */
abstract class AbstractStatus implements StatusInterface
{
    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @ODM\Field(type="hash")
     * @ORCHESTRA\Search(key="label", type="multiLanguages")
     */
    protected $labels;

    /**
     * @var bool
     *
     * @ODM\Field(type="boolean")
     * @ORCHESTRA\Search(key="published", type="boolean")
     */
    protected $published = false;

    /**
     * @var bool
     *
     * @ODM\Field(type="boolean")
     * @ORCHESTRA\Search(key="blocked_edition", type="boolean")
     */
    protected $blockedEdition = false;

    /**
     * @var ArrayCollection
     *
     * @ODM\ReferenceMany(targetDocument="OpenOrchestra\ModelInterface\Model\RoleInterface", mappedBy="fromStatus")
     */
    protected $fromRoles;

    /**
     * @var string
     *
     * @ODM\ReferenceMany(targetDocument="OpenOrchestra\ModelInterface\Model\RoleInterface", mappedBy="toStatus")
     */
    protected $toRoles;

    /**
     * @ODM\Field(type="boolean")
     * @ORCHESTRA\Search(key="initial", type="boolean")
     */
    protected $initial = false;

    /**
     * @ODM\Field(type="boolean")
     * @ORCHESTRA\Search(key="autoPublishFrom", type="boolean")
     */
    protected $autoPublishFrom = false;

    /**
     * @ODM\Field(type="boolean")
     * @ORCHESTRA\Search(key="autoUnpublishTo", type="boolean")
     */
    protected $autoUnpublishTo = false;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="display_color")
     */
    protected $displayColor;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->labels = array();
        $this->fromRoles = new ArrayCollection();
        $this->toRoles = new ArrayCollection();
    }

    /**
     * @return string
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
     * @param string $language
     * @param string $label
     */
    public function addLabel($language, $label)
    {
        if (is_string($language) && is_string($label)) {
            $this->labels[$language] = $label;
        }
    }

    /**
     * @param string $language
     */
    public function removeLabel($language)
    {
        if (is_string($language) && isset($this->labels[$language])) {
            unset($this->labels[$language]);
        }
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getLabel($language)
    {
        if (isset($this->labels[$language])) {
            return $this->labels[$language];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param array $labels
     */
    public function setLabels(array $labels)
    {
        foreach ($labels as $language => $label) {
            $this->addLabel($language, $label);
        }
    }

    /**
     * @param boolean $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $blockedEdition
     */
    public function setBlockedEdition($blockedEdition)
    {
        $this->blockedEdition = $blockedEdition;
    }

    /**
     * @return boolean
     */
    public function isBlockedEdition()
    {
        return $this->blockedEdition;
    }

    /**
     * @param boolean $initial
     */
    public function setInitial($initial)
    {
        $this->initial = $initial;
    }

    /**
     * @return boolean
     */
    public function isInitial()
    {
        return $this->initial;
    }

    /**
     * @param boolean $autoPublishFrom
     */
    public function setAutoPublishFrom($autoPublishFrom)
    {
        $this->autoPublishFrom = $autoPublishFrom;
    }

    /**
     * @return boolean
     */
    public function isAutoPublishFrom()
    {
        return $this->autoPublishFrom;
    }

    /**
     * @param boolean $autoUnpublishTo
     */
    public function setAutoUnpublishTo($autoUnpublishTo)
    {
        $this->autoUnpublishTo = $autoUnpublishTo;
    }

    /**
     * @return boolean
     */
    public function isAutoUnpublishTo()
    {
        return $this->autoUnpublishTo;
    }

    /**
     * @return ArrayCollection
     */
    public function getFromRoles()
    {
        return $this->fromRoles;
    }

    /**
     * @param RoleInterface $role
     */
    public function addFromRole(RoleInterface $role)
    {
        $this->fromRoles->add($role);
    }

    /**
     * @param RoleInterface $role
     */
    public function removeFromRole(RoleInterface $role)
    {
        $this->fromRoles->removeElement($role);
    }

    /**
     * @param RoleInterface $role
     */
    public function addToRole(RoleInterface $role)
    {
        $this->toRoles->add($role);
    }

    /**
     * @param RoleInterface $role
     */
    public function removeToRole(RoleInterface $role)
    {
        $this->toRoles->removeElement($role);
    }

    /**
     * @return ArrayCollection
     */
    public function getToRoles()
    {
        return $this->toRoles;
    }

    /**
     * @return mixed
     */
    public function getDisplayColor()
    {
        return $this->displayColor;
    }

    /**
     * @param mixed $displayColor
     */
    public function setDisplayColor($displayColor)
    {
        $this->displayColor = $displayColor;
    }
}

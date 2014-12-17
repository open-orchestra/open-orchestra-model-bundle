<?php

namespace PHPOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ModelInterface\Model\StatusInterface;
use PHPOrchestra\ModelInterface\Model\TranslatedValueInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class AbstractStatus
 */
abstract class AbstractStatus implements StatusInterface
{
    /**
     * @var string $id
     *
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var string $name
     *
     * @MongoDB\Field(type="string")
     */
    protected $name;

    /**
     * @MongoDB\EmbedMany(targetDocument="TranslatedValue")
     */
    protected $labels;

    /**
     * @var bool
     *
     * @MongoDB\Field(type="boolean")
     */
    protected $published = false;

    /**
     * @var ArrayCollection
     *
     * @MongoDB\ReferenceMany(targetDocument="PHPOrchestra\ModelBundle\Document\Role", mappedBy="fromStatus")
     */
    protected $fromRoles;

    /**
     * @var string
     *
     * @MongoDB\ReferenceMany(targetDocument="PHPOrchestra\ModelBundle\Document\Role", mappedBy="toStatus")
     */
    protected $toRoles;

    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $initial = false;

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    protected $displayColor;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->labels = new ArrayCollection();
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
     * @return ArrayCollection
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param TranslatedValueInterface $translatedValue
     */
    public function addLabel(TranslatedValueInterface $translatedValue)
    {
        $this->labels->add($translatedValue);
    }

    /**
     * @param TranslatedValueInterface $translatedValue
     */
    public function removeLabel(TranslatedValueInterface $translatedValue)
    {
        $this->labels->removeElement($translatedValue);
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
     * @return array
     */
    public function getTranslatedProperties()
    {
        return array(
            'getLabels'
        );
    }

    /**
     * @return ArrayCollection
     */
    public function getFromRoles()
    {
        return $this->fromRoles;
    }

    /**
     * @param Role $role
     */
    public function addFromRole(Role $role)
    {
        $this->fromRoles->add($role);
    }

    /**
     * @param Role $role
     */
    public function removeFromRole(Role $role)
    {
        $this->fromRoles->removeElement($role);
    }

    /**
     * @param Role $role
     */
    public function addToRole(Role $role)
    {
        $this->toRoles->add($role);
    }

    /**
     * @param Role $role
     */
    public function removeToRole(Role $role)
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

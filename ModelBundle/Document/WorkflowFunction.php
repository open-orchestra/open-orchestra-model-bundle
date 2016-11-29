<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use OpenOrchestra\Workflow\Model\WorkflowFunctionInterface;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;

/**
 * Class WorkflowFunction
 *
 * @ODM\Document(
 *   collection="workflow_function",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\WorkflowFunctionRepository"
 * )
 */
class WorkflowFunction implements WorkflowFunctionInterface
{
    use BlameableDocument;
    use TimestampableDocument;

    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ODM\Field(type="hash")
     * @ORCHESTRA\Search(key="name", type="multiLanguages")
     */
    protected $names;

    /**
     * @var Collection
     *
     * @ODM\ReferenceMany(targetDocument="OpenOrchestra\ModelInterface\Model\RoleInterface")
     */
    protected $roles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->names = array();
        $this->initCollections();
    }

    /**
     * Clone the element
     */
    public function __clone()
    {
        $this->initCollections();
    }

    protected function initCollections() {
        $this->roles = new ArrayCollection();
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
     * @return Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param RoleInterface $role
     */
    public function addRole(RoleInterface $role)
    {
        $this->roles->add($role);
    }

    /**
     * @param RoleInterface $role
     */
    public function removeRole(RoleInterface $role)
    {
        $this->roles->remove($role);
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\WorkflowFunction\Model\AuthorizationInterface;
use OpenOrchestra\WorkflowFunction\Model\WorkflowFunctionInterface;

/**
 * Description of Authorization
 *
 * @ODM\EmbeddedDocument
 */
class Authorization implements AuthorizationInterface
{
    /**
     * @var string $referenceId
     *
     * @ODM\Field(type="string")
     */
    protected $referenceId;

    /**
     * @var Collection $workflowFunctions
     *
     * @ODM\ReferenceMany(targetDocument="OpenOrchestra\WorkflowFunction\Model\WorkflowFunctionInterface")
     */
    protected $workflowFunctions;

    /**
     * @var boolean $owner
     *
     * @ODM\Field(type="boolean")
     */
    protected $owner;

    /**
     * Constructor
     */
    public function __construct()
    {
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
        $this->workflowFunctions = new ArrayCollection();
    }

    /**
     * Set referenceId
     *
     * @param string $referenceId
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
    }

    /**
     * Get referenceId
     *
     * @return string
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    /**
     * Set workflowFunctions
     *
     * @param Collection $workflowFunctions
     */
    public function setWorkflowFunctions(Collection $workflowFunctions)
    {
        $this->workflowFunctions = $workflowFunctions;
    }

    /**
     * Get workflowFunctions
     *
     * @return Collection
     */
    public function getWorkflowFunctions()
    {
        return $this->workflowFunctions;
    }

    /**
     * @param WorkflowFunctionInterface $workflowFunction
     */
    public function addWorkflowFunction(WorkflowFunctionInterface $workflowFunction)
    {
        $this->workflowFunctions->add($workflowFunction);
    }

    /**
     * Set owner
     *
     * @param boolean $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return boolean
     */
    public function isOwner()
    {
        return $this->owner;
    }
}

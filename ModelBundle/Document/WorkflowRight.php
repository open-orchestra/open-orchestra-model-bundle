<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Workflow\Model\WorkflowRightInterface;
use OpenOrchestra\Workflow\Model\AuthorizationInterface;

/**
 * Class WorkflowRight
 *
 * @ODM\Document(
 *   collection="workflow_right",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\WorkflowRightRepository"
 * )
 */
class WorkflowRight implements WorkflowRightInterface
{
    /**
     * @var string $userId
     *
     * @ODM\Field(type="string")
     */
    protected $userId;

    /**
     * @var Collection $authorizations
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\Workflow\Model\AuthorizationInterface")
     */
    protected $authorizations;

    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

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

    /**
     * Initialize collections
     */
    protected function initCollections()
    {
        $this->authorizations = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get userId
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set authorizations
     *
     * @param Collection $authorizations
     */
    public function setAuthorizations(Collection $authorizations)
    {
        $this->authorizations = $authorizations;
    }

    /**
     * Get authorizations
     *
     * @return Collection
     */
    public function getAuthorizations()
    {
        return $this->authorizations;
    }

    /**
     * Remove authorization
     *
     * @param AuthorizationInterface $authorization
     */
    public function removeAuthorization(AuthorizationInterface $authorization)
    {
        $this->authorizations->removeElement($authorization);
    }

    /**
     * Add authorization
     *
     * @param AuthorizationInterface $authorization
     */
    public function addAuthorization(AuthorizationInterface $authorization)
    {
        $this->authorizations[] = $authorization;
    }
}

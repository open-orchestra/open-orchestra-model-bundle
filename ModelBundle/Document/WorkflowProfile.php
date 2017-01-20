<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Class WorkflowProfile
 *
 * @ODM\Document(
 *   collection="workflow_profile",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\WorkflowProfileRepository"
 * )
 */
class WorkflowProfile implements WorkflowProfileInterface
{
    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var array $labels
     *
     * @ODM\Field(type="hash")
     */
    protected $labels;

    /**
     * @var array $descriptions
     *
     * @ODM\Field(type="hash")
     */
    protected $descriptions;

    /**
     * @var ArrayCollection
     *
     * @ODM\EmbedMany(
     *  targetDocument="OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface"
     * )
     */
    protected $transitions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initCollections();
        $this->labels = array();
        $this->descriptions = array();
    }

    /**
     * Clone the element
     */
    public function __clone()
    {
        $this->initCollections();
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
        if (is_string($language) && isset($this->labels[$language])) {
            unset($this->labels[$language]);
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

    /**
     * @param WorkflowTransitionInterface $transition
     */
    public function addTransition(WorkflowTransitionInterface $transition)
    {
        $this->transitions->add($transition);
    }

    public function setTransitions(array $transitions) {
        $this->transitions = $transitions;
    }

    /**
     * @param StatusInterface $fromStatus
     * @param StatusInterface $toStatus
     *
     * @return boolean
     */
    public function hasTransition(StatusInterface $fromStatus, StatusInterface $toStatus)
    {
        foreach ($this->transitions as $transition) {
            if ($transition->getStatusFrom()->getId() === $fromStatus->getId()
                && $transition->getStatusTo()->getId() === $toStatus->getId()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTransitions()
    {
        return $this->transitions;
    }

    /**
     * Initialize collections
     */
    protected function initCollections() {
        $this->transitions = new ArrayCollection();
    }
}

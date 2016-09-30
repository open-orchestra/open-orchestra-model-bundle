<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\HistoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of History
 *
 * @ODM\EmbeddedDocument
 */
class History implements HistoryInterface
{
    /**
     * @var UserInterface
     *
     * @ODM\ReferenceOne(targetDocument="Symfony\Component\Security\Core\User\UserInterface")
     */
    protected $user;

    /**
     * @var string $updatedAt
     *
     * @ODM\Date
     */
    protected $updatedAt;

    /**
     * @var string $eventType
     *
     * @ODM\Field(type="string")
     */
    protected $eventType;

    /**
     * Set user
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return UserInterface $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets updatedAt
     *
     * @param  \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Returns updatedAt
     *
     * @return \Datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets eventType
     *
     * @param  string $eventType
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
    }

    /**
     * Returns eventType
     *
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }
}

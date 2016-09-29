<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\HistoryInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;

/**
 * Description of History
 *
 * @ODM\EmbeddedDocument
 */
class History implements HistoryInterface
{
    /**
     * @var string $user
     *
     * @ODM\ReferenceOne(targetDocument="OpenOrchestra\UserBundle\Model\UserInterface")
     */
    protected $user;

    /**
     * @var string $updatedAt
     *
     * @ODM\Date
     */
    protected $updatedAt;

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
     * Sets updatedAt.
     *
     * @param  \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Returns updatedAt.
     *
     * @return \Datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}

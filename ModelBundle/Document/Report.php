<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\ReportInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;

/**
 * Description of Report
 *
 * @ODM\EmbeddedDocument
 */
class Report implements ReportInterface
{
    /**
     * @var string $user
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

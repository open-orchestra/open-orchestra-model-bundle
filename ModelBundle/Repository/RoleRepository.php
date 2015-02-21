<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;

/**
 * Class RoleRepository
 */
class RoleRepository extends DocumentRepository implements RoleRepositoryInterface
{
    /**
     * Find the role that connect fromStatus to toStatus
     * 
     * @param StatusInterface $fromStatus
     * @param StatusInterface $toStatus
     *
     * @return RoleInterface
     */
    public function findOneByFromStatusAndToStatus(StatusInterface $fromStatus, StatusInterface $toStatus)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->field('fromStatus.id')->equals($fromStatus->getId());
        $qb->field('toStatus.id')->equals($toStatus->getId());

        return $qb->getQuery()->getSingleResult();
    }
}

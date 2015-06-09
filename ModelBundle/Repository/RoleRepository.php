<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\PaginateAndSearchFilterTrait;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;

/**
 * Class RoleRepository
 */
class RoleRepository extends AbstractRepository implements RoleRepositoryInterface
{
    use PaginateAndSearchFilterTrait;

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
        $qb = $this->createQueryBuilder();
        $qb->field('fromStatus.id')->equals($fromStatus->getId());
        $qb->field('toStatus.id')->equals($toStatus->getId());

        return $qb->getQuery()->getSingleResult();
    }
}

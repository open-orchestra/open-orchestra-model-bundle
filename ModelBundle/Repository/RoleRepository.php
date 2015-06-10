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
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'fromStatus.$id' => new \MongoId($fromStatus->getId()),
                'toStatus.$id'   => new \MongoId($toStatus->getId()),
            )
        );
        return $this->singleHydrateAggregateQuery($qa);
    }
}

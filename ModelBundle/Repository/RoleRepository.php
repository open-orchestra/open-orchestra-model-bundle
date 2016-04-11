<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class RoleRepository
 */
class RoleRepository extends AbstractAggregateRepository implements RoleRepositoryInterface
{
    use PaginationTrait;

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

    /**
     * Find the roles that don't contain fromStatus and toStatus
     *
     * @return array
     */
    public function findAccessRole()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'fromStatus' => null,
                'toStatus' => null,
            )
        );

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * Find the roles that don't contain fromStatus and toStatus
     *
     * @return array
     */
    public function findWorkflowRole()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'fromStatus' => array(
                    '$ne' => null
                ),
                'toStatus' => array(
                    '$ne' => null
                ),
            )
        );

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param StatusInterface $status
     *
     * @return bool
     */
    public function hasStatusedElement(StatusInterface $status)
    {
        $role = $this->findOneBy(array('fromStatus.$id' => new \MongoId($status->getId())));
        if ($role instanceof RoleInterface) {
            return true;
        }

        $role = $this->findOneBy(array('toStatus.$id' => new \MongoId($status->getId())));

        return $role instanceof RoleInterface;
    }
}

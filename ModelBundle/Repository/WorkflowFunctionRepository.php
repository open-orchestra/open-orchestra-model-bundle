<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\ModelInterface\Repository\WorkflowFunctionRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowFunctionInterface;

/**
 * Class WorkflowFunctionRepository
 */
class WorkflowFunctionRepository extends AbstractAggregateRepository implements WorkflowFunctionRepositoryInterface
{
    use PaginationTrait;

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findAllWorkflowFunction()
    {
        $qb = $this->createQueryBuilder();

        return $qb->getQuery()->execute();
    }

    /**
     * @param RoleInterface $role
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findByRole(RoleInterface $role)
    {
        $qb = $this->createQueryBuilder();
        $qb->field('roles.id')->equals($role->getId());

        return $qb->getQuery()->execute();
    }

    /**
     * @param RoleInterface $status
     *
     * @return bool
     */
    public function hasElementWithRole(RoleInterface $role)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('roles.$id' => new \MongoId($role->getId())));
        $workflowFunction = $this->singleHydrateAggregateQuery($qa);

        return $workflowFunction instanceof WorkflowFunctionInterface;
    }
}

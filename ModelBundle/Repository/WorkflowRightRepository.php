<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\ModelInterface\Model\WorkflowFunctionInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowRightInterface;
use OpenOrchestra\ModelInterface\Repository\WorkflowRightRepositoryInterface;

/**
 * Class WorkflowRightRepositoryInterface
 */
class WorkflowRightRepository extends AbstractAggregateRepository implements WorkflowRightRepositoryInterface
{
    /**
     * @param string $userId
     *
     * @return \OpenOrchestra\ModelInterface\Model\WorkflowRightInterface
     */
    public function findOneByUserId($userId)
    {
        $qb = $this->createQueryBuilder();
        $qb->field('userId')->equals($userId);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param WorkflowFunctionInterface $workflowFunction
     *
     * @return bool
     */
    public function hasElementWithWorkflowFunction(WorkflowFunctionInterface $workflowFunction)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array(
            'authorizations.workflowFunctions.$id' => new \MongoId($workflowFunction->getId())
        ));
        $workflowRight = $this->singleHydrateAggregateQuery($qa);

        return $workflowRight instanceof WorkflowRightInterface;
    }
}

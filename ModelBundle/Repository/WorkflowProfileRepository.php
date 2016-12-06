<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface;

/**
 * Class WorkflowProfileRepository
 */
class WorkflowProfileRepository extends AbstractAggregateRepository implements WorkflowProfileRepositoryInterface
{
    /**
     * Test is $transition exists
     *
     * @param  WorkflowTransitionInterface $transition
     *
     * @return boolean
     */
    public function hasTransition(WorkflowTransitionInterface $transition)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('transitions' => $transition));
        $profile = $this->singleHydrateAggregateQuery($qa);

        return $profile instanceof WorkflowProfileInterface;
    }
}

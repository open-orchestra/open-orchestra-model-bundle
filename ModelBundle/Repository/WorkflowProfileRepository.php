<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Class WorkflowProfileRepository
 */
class WorkflowProfileRepository extends AbstractAggregateRepository implements WorkflowProfileRepositoryInterface
{
    /**
     * Test is a transition ($fromStatus, $toStatus) exists
     *
     * @param StatusInterface $fromStatus
     * @param StatusInterface $toStatus
     *
     * @return boolean
     */
    public function hasTransition(StatusInterface $fromStatus, StatusInterface $toStatus)
    {
        $statusClassMetaData = $this->dm->getClassMetadata(get_class($toStatus));

        $qa = $this->createAggregationQuery();
        $qa->match(
          array(
               'transitions' => array(
                   'statusFrom' => array (
                       '$ref' => $statusClassMetaData->getCollection(),
                       '$id' => new \MongoId($fromStatus->getId()),
                       '$db' => $this->dm->getDocumentDatabase($statusClassMetaData->name)->getName()
                   ),
                   'statusTo' => array (
                       '$ref' => $statusClassMetaData->getCollection(),
                       '$id' => new \MongoId($toStatus->getId()),
                       '$db' => $this->dm->getDocumentDatabase($statusClassMetaData->name)->getName()
                   )
                )
            )
        );

        $profile = $this->singleHydrateAggregateQuery($qa);

        return $profile instanceof WorkflowProfileInterface;
    }
}

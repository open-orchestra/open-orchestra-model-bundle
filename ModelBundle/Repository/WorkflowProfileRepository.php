<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Solution\MongoAggregation\Pipeline\Stage;

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

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregationQuery();

        $this->filterSearch($configuration, $qa);

        $order = $configuration->getOrder();
        if (!empty($order)) {
            $qa->sort($order);
        }

        $qa->skip($configuration->getSkip());
        $qa->limit($configuration->getLimit());

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return int
     */
    public function countWithFilter(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregationQuery();
        $this->filterSearch($configuration, $qa);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @return int
     */
    public function count()
    {
        $qa = $this->createAggregationQuery();

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param Stage                       $qa
     *
     * @return array
     */
    protected function filterSearch(PaginateFinderConfiguration $configuration, Stage $qa)
    {
        $label = $configuration->getSearchIndex('label');
        $language = $configuration->getSearchIndex('language');

        if (null !== $label && '' !== $label && null !== $language && '' !== $language) {
            $qa->match(array('labels.' . $language => new \MongoRegex('/.*'.$label.'.*/i')));
        }

        return $qa;
    }

    /**
     * @param array $workflowProfileIds
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function removeWorkflowProfiles(array $workflowProfileIds)
    {
        array_walk($workflowProfileIds, function(&$workflowProfileId) {$workflowProfileId = new \MongoId($workflowProfileId);});

        $qb = $this->createQueryBuilder();
        $qb->remove()
            ->field('id')->in($workflowProfileIds)
            ->getQuery()
            ->execute();
    }
}

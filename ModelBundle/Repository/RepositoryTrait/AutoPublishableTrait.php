<?php

namespace OpenOrchestra\ModelBundle\Repository\RepositoryTrait;

use OpenOrchestra\ModelInterface\Model\StatusInterface;
/**
 * Trait AutoPublishableTrait
 */
trait AutoPublishableTrait
{
    /**
     * Find all element (in all versions and all languages) ready to be auto-published
     *
     * @param string $siteId
     * @param array  $fromStatus
     *
     * @return array
     */
    public function findElementToAutoPublish($siteId, array $fromStatus)
    {
        $date = new \Mongodate(strtotime(date('d F Y')));

        $statusIds = array();
        foreach($fromStatus as $status) {
            $statusIds[] = new \MongoId($status->getId());
        }

        $qa = $this->createAggregationQuery();

        $filter = array(
            'siteId' => $siteId,
            'deleted' => false,
            'status._id' => array('$in' => $statusIds),
            'status.outOfWorkflow' => false,
            'publishDate' => array('$lte' => $date),
            '$or' => array(
                array('unpublishDate' => array('$exists' => false)),
                array('unpublishDate' => array('$gte' => $date))
            )
        );

        $qa->match($filter);
        $qa->sort(array('createdAt' => 1));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * Find all elements (in all versions and all langauges) ready to be auto-unpublished
     *
     * @param string          $siteId
     * @param StatusInterface $publishedStatus
     *
     * @return array
     */
    public function findElementToAutoUnpublish($siteId, StatusInterface $publishedStatus)
    {
        $date = new \Mongodate(strtotime(date('d F Y')));
        $statusId = new \MongoId($publishedStatus->getId());

        $qa = $this->createAggregationQuery();

        $filter = array(
            'siteId' => $siteId,
            'deleted' => false,
            'status._id' => $statusId,
            'status.outOfWorkflow' => false,
            'unpublishDate' => array('$lte' => $date)
        );

        $qa->match($filter);
        $qa->sort(array('createdAt' => 1));

        return $this->hydrateAggregateQuery($qa);
    }
}

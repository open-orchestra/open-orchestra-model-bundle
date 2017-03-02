<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use OpenOrchestra\ModelInterface\Repository\TrashItemRepositoryInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class TrashItemRepository
 */
class TrashItemRepository extends AbstractAggregateRepository implements TrashItemRepositoryInterface
{
    /**
     * @param $entityId
     *
     * @return TrashItemInterface
     */
    public function findByEntity($entityId)
    {
        return $this->findOneBy(array('entity.$id' => new \MongoId($entityId)));
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $siteId
     *
     * @return array
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration, $siteId)
    {
        $qa = $this->createAggregationQuery();

        $this->filterSearch($configuration, $qa, $siteId);

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
     * @param string                      $siteId
     *
     * @return int
     */
    public function countWithFilter(PaginateFinderConfiguration $configuration, $siteId)
    {
        $qa = $this->createAggregationQuery();
        $this->filterSearch($configuration, $qa, $siteId);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string $siteId
     *
     * @return int
     */
    public function countBySite($siteId)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('siteId' => $siteId));

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param array $trashItemIds
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function removeTrashItems(array $trashItemIds)
    {
        array_walk($trashItemIds, function(&$trashItemId) {$trashItemId = new \MongoId($trashItemId);});

        $qb = $this->createQueryBuilder();
        $qb->remove()
            ->field('id')->in($trashItemIds)
            ->getQuery()
            ->execute();
    }


    /**
     * @param PaginateFinderConfiguration $configuration
     * @param Stage                       $qa
     * @param string                      $siteId
     *
     * @return array
     */
    protected function filterSearch(PaginateFinderConfiguration $configuration, Stage $qa, $siteId)
    {
        $qa->match(array('siteId' => $siteId));

        $name = $configuration->getSearchIndex('name');
        if (null !== $name && '' !== $name) {
            $qa->match(array('name' => new \MongoRegex('/.*'.$name.'.*/i')));
        }

        return $qa;
    }

}

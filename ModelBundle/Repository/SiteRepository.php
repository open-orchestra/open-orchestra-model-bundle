<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class SiteRepository
 */
class SiteRepository extends AbstractAggregateRepository implements SiteRepositoryInterface
{
    /**
     * @param string $siteId
     *
     * @return SiteInterface
     */
    public function findOneBySiteId($siteId)
    {
        return $this->findOneBy(array('siteId' => $siteId));
    }

    /**
     * @param array $sitesIds
     *
     * @return array
     */
    public function findBySiteIds(array $sitesIds)
    {
        return parent::findBy(
            array(
                'siteId' => array('$in' => $sitesIds),
                'deleted' => false
            )
        );
    }


    /**
     * @param boolean $deleted
     *
     * @return array
     */
    public function findByDeleted($deleted)
    {
        return $this->findBy(array('deleted' => $deleted));
    }

    /**
     * @param string $domain
     *
     * @return \Doctrine\ODM\MongoDB\Cursor
     */
    public function findByAliasDomain($domain)
    {
        $database = $this->dm->getDocumentDatabase($this->documentName);
        $collectionName = $this->dm->getClassMetadata($this->documentName)->collection;

        $map = new \MongoCode(
            'function(){
                for (var i in this.aliases) {
                    if (this.aliases[i].domain == domain) {
                        emit(this.siteId, this.siteId);
                    }
                }
            }'
        );
        $reduce = new \MongoCode("function(key, values) { return values[0]; }");

        $commandResult = $database->command(array(
            "mapreduce" => $collectionName,
            "map" => $map,
            "reduce" => $reduce,
            "out" => array("inline" => 1),
            "scope" => array(
                "domain" => "$domain"
            )
        ));

        $ids = array();
        if (is_array($commandResult) && array_key_exists('ok', $commandResult) && $commandResult['ok'] == 1) {
            foreach ($commandResult['results'] as $siteId) {
                $ids[] = $siteId['_id'];
            }
        }

        $qb = $this->createQueryBuilder();
        $qb->field('siteId')->in($ids);

        return $qb->getQuery()->execute();
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array|null                  $siteIds
     *
     * @return array
     */
    public function findForPaginateFilterBySiteIds(PaginateFinderConfiguration $configuration, array $siteIds = null)
    {
        $qa = $this->createAggregateQueryWithDeletedFilter(false);
        if (null !== $siteIds) {
            $qa->match(array('siteId' => array('$in' => $siteIds)));
        }

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
     * @param array|null $siteIds
     *
     * @return int
     */
    public function countFilterBySiteIds(array $siteIds = null)
    {
        $qa = $this->createAggregateQueryWithDeletedFilter(false);
        if (null !== $siteIds) {
            $qa->match(array('siteId' => array('$in' => $siteIds)));
        }

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param array|null                  $siteIds
     *
     * @return int
     */
    public function countWithFilterAndSiteIds(PaginateFinderConfiguration $configuration, array $siteIds = null)
    {
        $qa = $this->createAggregateQueryWithDeletedFilter(false);
        if (null !== $siteIds) {
            $qa->match(array('siteId' => array('$in' => $siteIds)));
        }
        $this->filterSearch($configuration, $qa);

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
        $search = $configuration->getSearchIndex('name');
        if (null !== $search && $search !== '') {
            $qa->match(array('name' => new \MongoRegex('/.*'.$search.'.*/i')));
        }

        return $qa;
    }

    /**
     * @param $deleted
     *
     * @return \Solution\MongoAggregation\Pipeline\Stage
     */
    protected function createAggregateQueryWithDeletedFilter($deleted)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('deleted' => $deleted));

        return $qa;
    }
}

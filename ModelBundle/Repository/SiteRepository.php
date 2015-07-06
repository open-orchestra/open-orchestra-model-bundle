<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\PaginateAndSearchFilterTrait;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;

/**
 * Class SiteRepository
 */
class SiteRepository extends AbstractRepository implements SiteRepositoryInterface
{
    use PaginateAndSearchFilterTrait;

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
     * @param $siteId
     *
     * @return SiteInterface
     */
    public function findOneBySiteIdNotDeleted($siteId)
    {
        return $this->findOneBy(array('siteId' => $siteId, 'deleted' => false));
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
     * @param boolean     $deleted
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     * @param array|null  $order
     * @param int|null    $skip
     * @param int|null    $limit
     *
     * @deprecated will be removed in 0.3.0, use findByDeletedForPaginate instead
     *
     * @return array
     */
    public function findByDeletedForPaginateAndSearch($deleted, $descriptionEntity = null, $columns = null, $search = null, $order = null, $skip = null, $limit = null)
    {
        $configuration = PaginateFinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);
        $configuration->setPaginateConfiguration($order, $skip, $limit);

        return $this->findByDeletedForPaginate($deleted, $configuration);
    }

    /**
     * @param boolean                     $deleted
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    public function findByDeletedForPaginate($deleted, PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregateQueryWithDeletedFilter($deleted);
        $qa = $this->generateFilterForPaginate($qa, $configuration);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param boolean $deleted
     *
     * @return int
     */
    public function countByDeleted($deleted)
    {
        $qa = $this->createAggregateQueryWithDeletedFilter($deleted);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param boolean      $deleted
     * @param array|null   $columns
     * @param array|null   $descriptionEntity
     * @param array|null   $search
     *
     * @deprecated will be removed in 0.3.0, use countWithSearchFilterByDeleted instead
     *
     * @return int
     */
    public function countByDeletedWithSearchFilter($deleted, $descriptionEntity = null, $columns = null, $search = null)
    {
        $configuration = FinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);

        return $this->countWithSearchFilterByDeleted($deleted, $configuration);
    }

    /**
     * @param boolean             $deleted
     * @param FinderConfiguration $configuration
     *
     * @return int
     */
    public function countWithSearchFilterByDeleted($deleted, FinderConfiguration $configuration)
    {
        $qa = $this->createAggregateQueryWithDeletedFilter($deleted);
        $qa = $this->generateFilter($qa, $configuration);

        return $this->countDocumentAggregateQuery($qa);
    }
    /**
     * @param string $domain
     *
     * @return ReadSiteInterface
     */
    public function findByAliasDomain($domain)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('aliases.domain' => $domain));

        return $this->singleHydrateAggregateQuery($qa);
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

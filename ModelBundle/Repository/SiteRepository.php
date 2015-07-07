<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class SiteRepository
 */
class SiteRepository extends AbstractAggregateRepository implements SiteRepositoryInterface
{
    use PaginationTrait;

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

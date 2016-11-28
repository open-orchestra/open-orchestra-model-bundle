<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Repository\RedirectionRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class RedirectionRepository
 */
class RedirectionRepository extends AbstractAggregateRepository implements RedirectionRepositoryInterface
{
    use PaginationTrait;

    /**
     * @param string $nodeId
     * @param string $locale
     * @param string $siteId
     *
     * @return array
     */
    public function findByNode($nodeId, $locale, $siteId){
        $qa = $this->createAggregationQuery();

        $qa->match(array(
            'nodeId' => $nodeId,
            'locale' => $locale,
            'siteId' => $siteId,
        ));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $siteId
     *
     * @return array
     */
    public function findBySiteId($siteId){
        $qa = $this->createAggregationQuery();

        $qa->match(array(
            'siteId' => $siteId,
        ));

        return $this->hydrateAggregateQuery($qa);
    }
}

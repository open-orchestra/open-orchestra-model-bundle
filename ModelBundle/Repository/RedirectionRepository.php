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
     *
     * @return array
     */
    public function findByNode($nodeId, $locale){
        $qa = $this->createAggregationQuery();

        $qa->match(array(
            'nodeId' => $nodeId,
            'locale' => $locale,
        ));

        return $this->hydrateAggregateQuery($qa);
    }
}

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
     * @param int    $nodeVersion
     * @param string $language
     *
     * @return array
     */
    public function findByNode($nodeId, $nodeVersion, $language){
        $qa = $this->createAggregationQuery();

        $qa->match(array(
            'nodeId' => $nodeId,
            'nodeVersion' => $nodeVersion,
            'locale' => $language,
        ));

        return $this->hydrateAggregateQuery($qa);
    }
}

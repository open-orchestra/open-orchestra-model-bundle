<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Repository\BlockRepositoryInterface;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class BlockRepository
 */
class BlockRepository extends AbstractAggregateRepository implements BlockRepositoryInterface
{
    /**
     * @return array
     */
    public function findTransverse(){
        $qa = $this->createAggregationQuery();

        $qa->match(array(
            'transverse' => true
        ));

        return $this->hydrateAggregateQuery($qa);
    }
}

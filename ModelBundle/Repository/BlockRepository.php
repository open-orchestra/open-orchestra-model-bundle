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
     * @param string $id
     *
     * @return null|\OpenOrchestra\ModelInterface\Model\ReadBlockInterface
     */
    public function findById($id) {
        return $this->find(new \MongoId($id));
    }

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

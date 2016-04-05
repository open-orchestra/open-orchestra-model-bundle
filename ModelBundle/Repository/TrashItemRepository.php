<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use OpenOrchestra\ModelInterface\Repository\TrashItemRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class TrashItemRepository
 */
class TrashItemRepository extends AbstractAggregateRepository implements TrashItemRepositoryInterface
{
    use PaginationTrait;

    /**
     * @param $entityId
     *
     * @return TrashItemInterface
     */
    public function findByEntity($entityId)
    {
        return $this->findOneBy(array('entity.$id' => new \MongoId($entityId)));
    }
}

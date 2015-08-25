<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Repository\TrashItemRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class TrashItemRepository
 */
class TrashItemRepository extends AbstractAggregateRepository implements TrashItemRepositoryInterface
{
    use PaginationTrait;
}

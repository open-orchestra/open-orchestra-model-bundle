<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Repository\TrashCanRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class TrashCanRepository
 */
class TrashCanRepository extends AbstractAggregateRepository implements TrashCanRepositoryInterface
{
    use PaginationTrait;
}

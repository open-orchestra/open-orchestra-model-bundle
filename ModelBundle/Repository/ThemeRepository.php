<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Repository\ThemeRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class ThemeRepository
 */
class ThemeRepository extends AbstractAggregateRepository implements ThemeRepositoryInterface
{
    use PaginationTrait;
}

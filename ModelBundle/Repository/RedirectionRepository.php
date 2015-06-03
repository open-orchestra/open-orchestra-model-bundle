<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\PaginateAndSearchFilterTrait;
use OpenOrchestra\ModelInterface\Repository\RedirectionRepositoryInterface;

/**
 * Class RedirectionRepository
 */
class RedirectionRepository extends AbstractRepository implements RedirectionRepositoryInterface
{
    use PaginateAndSearchFilterTrait;
}

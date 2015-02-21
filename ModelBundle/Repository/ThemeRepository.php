<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\ModelInterface\Repository\ThemeRepositoryInterface;

/**
 * Class ThemeRepository
 */
class ThemeRepository extends DocumentRepository implements ThemeRepositoryInterface
{

}

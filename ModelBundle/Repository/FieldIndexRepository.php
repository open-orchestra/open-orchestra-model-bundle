<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\ModelInterface\Repository\FieldIndexRepositoryInterface;

/**
 * Class FieldIndexRepository
 */
class FieldIndexRepository extends DocumentRepository implements FieldIndexRepositoryInterface
{
    /**
     * Get All field that will be a link
     *
     * @return array
     */
    public function findAllLink()
    {
        return $this->findBy(array('link' => true));
    }
}

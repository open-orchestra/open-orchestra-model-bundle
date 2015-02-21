<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\ModelInterface\Repository\ListIndexRepositoryInterface;

/**
 * Class ListIndexRepository
 */
class ListIndexRepository extends DocumentRepository implements ListIndexRepositoryInterface
{
    /**
     * @param string $docId
     *
     * @return mixed
     */
    public function removeByDocId($docId)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->remove();
        $qb->addOr($qb->expr()->field('nodeId')->equals($docId));
        $qb->addOr($qb->expr()->field('contentId')->equals($docId));

        return $qb->getQuery()->execute();
    }
}

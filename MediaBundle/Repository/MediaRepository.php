<?php

namespace PHPOrchestra\MediaBundle\Repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Class MediaRepository
 */
class MediaRepository extends DocumentRepository
{
    /**
     * @param string $folderId
     *
     * @return Collection
     */
    public function findByFolderId($folderId)
    {
        $qb = $this->createQueryBuilder('m');

        $qb->field('mediaFolder.id')->equals($folderId);

        return $qb->getQuery()->execute();
    }
}
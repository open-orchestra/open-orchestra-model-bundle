<?php

namespace OpenOrchestra\ModelBundle\Repository\RepositoryTrait;

/**
 * Trait UseTrackableTrait
 */
trait UseTrackableTrait
{
    /**
     * @param string $entityId
     * @param string $entityType
     *
     * @return array
     */
    public function findByUsedInEntity($entityId, $entityType)
    {
        $qb = $this->createQueryBuilder();

        $qb->field('useReferences.' . $entityType . '.' . $entityId)->exists('true');

        return $qb->getQuery()->execute();
    }
}

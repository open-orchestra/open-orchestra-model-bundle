<?php

namespace OpenOrchestra\ModelBundle\Repository\RepositoryTrait;

use Doctrine\MongoDB\Query\Builder;

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

    /**
     * @param Builder $qb
     * @param string $entityType
     * @param string $referenceEntityId
     *
     * @return Builder $qb
     */
    protected function addUpdateUseReferenceField(Builder $qb, $entityType, $referenceEntityId)
    {
        $qb->field('useReferences.'.$entityType.'.'.$referenceEntityId)->set($referenceEntityId);

        return $qb;
    }

}

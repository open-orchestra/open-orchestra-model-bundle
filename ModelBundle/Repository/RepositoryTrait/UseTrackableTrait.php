<?php

namespace OpenOrchestra\ModelBundle\Repository\RepositoryTrait;

use OpenOrchestra\ModelInterface\Model\UseTrackableInterface;

/**
 * Trait UseTrackableTrait
 */
trait UseTrackableTrait
{
    /**
     * @param string $nodeId
     *
     * @return array
     */
    public function findUsedInNode($nodeId)
    {
        $qb = $this->createQueryBuilder();

        $qb->field('useReferences.' . UseTrackableInterface::KEY_NODE . '.' . $nodeId)->exists('true');

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $contentId
     *
     * @return array
     */
    public function findUsedInContent($contentId)
    {
        $qb = $this->createQueryBuilder();

        $qb->field('useReferences.' . UseTrackableInterface::KEY_CONTENT . '.' . $contentId)->exists('true');

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $contentId
     *
     * @return array
     */
    public function findUsedInContentType($contentId)
    {
        $qb = $this->createQueryBuilder();

        $qb->field('useReferences.' . UseTrackableInterface::KEY_CONTENT_TYPE . '.' . $contentId)->exists('true');

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $mediaId
     *
     * @return array
     */
    public function findUsedInMedia($mediaId)
    {
        $qb = $this->createQueryBuilder();

        $qb->field('useReferences.' . UseTrackableInterface::KEY_MEDIA . '.' . $mediaId)->exists('true');

        return $qb->getQuery()->execute();
    }
}

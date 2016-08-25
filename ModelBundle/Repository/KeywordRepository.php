<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\ModelInterface\Model\UseTrackableInterface;

/**
 * Class KeywordRepository
 */
class KeywordRepository extends AbstractAggregateRepository implements KeywordRepositoryInterface
{
    use PaginationTrait;

    /**
     * @param string $label
     *
     * @return KeywordInterface|null
     */
    public function findOneByLabel($label)
    {
        return $this->findOneBy(array('label' => $label));
    }

    /**
     * @return mixed
     */
    public function getManager()
    {
        return $this->getDocumentManager();
    }

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

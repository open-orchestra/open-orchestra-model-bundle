<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

/**
 * Class ContentTypeRepository
 */
class ContentTypeRepository extends AbstractRepository implements ContentTypeRepositoryInterface
{
    /**
     * @deprecated use findOneByContentTypeIdInLastVersion to get the last version
     * 
     * @param string   $contentType
     * @param int|null $version
     * 
     * @return array|null|object
     */
    public function findOneByContentTypeIdAndVersion($contentType, $version = null)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->field('contentTypeId')->equals($contentType);

        $qb->sort('version', 'desc');
        if ($version) {
            $qb->field('version')->equals($version);
        }

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @return array
     */
    public function findAllByDeletedInLastVersion()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->field('deleted')->equals(false);

        $list = $qb->getQuery()->execute();
        $contentTypes = array();

        foreach ($list as $contentType) {
            if (empty($contentTypes[$contentType->getContentTypeId()])) {
                $contentTypes[$contentType->getContentTypeId()] = $contentType;
            }
            if ($contentTypes[$contentType->getContentTypeId()]->getVersion() < $contentType->getVersion()) {
                $contentTypes[$contentType->getContentTypeId()] = $contentType;
            }
        }

        return $contentTypes;
    }

    /**
     * @param string   $contentType
     *
     * @return ContentTypeInterface
     */
    public function findOneByContentTypeIdInLastVersion($contentType)
    {
        $qb = $this->createQueryBuilder('n');

        $qb->field('contentTypeId')->equals($contentType);
        $qb->sort('version', 'desc');

        return $qb->getQuery()->getSingleResult();
    }
}

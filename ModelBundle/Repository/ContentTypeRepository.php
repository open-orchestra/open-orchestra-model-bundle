<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

/**
 * Class ContentTypeRepository
 */
class ContentTypeRepository extends DocumentRepository implements ContentTypeRepositoryInterface
{
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
     * @param int|null $version
     * 
     * @return array|null|object
     */
    public function findOneByContentTypeIdInLastVersion($contentType)
    {
        $qb = $this->createQueryBuilder('n');

        $qb->field('contentTypeId')->equals($contentType);
        $qb->sort('version', 'desc');

        return $qb->getQuery()->getSingleResult();
    }
}

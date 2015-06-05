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
     * @param $language
     * 
     * @return array
     */
    public function findAllByDeletedInLastVersion($language = null)
    {
        $qa = $this->createAggregationQuery();

        $qa->match(
            array(
                'deleted' => false
            )
        );

        $elementName = 'contentType';
        $qa->group(array(
            '_id' => array('contentTypeId' => '$contentTypeId'),
            'version' => array('$max' => '$version'),
            $elementName => array('$last' => '$$ROOT')
        ));

        if ($language) {
            $qa->sort(
                array(
                    $elementName . '.names.' . $language. '.value' => 1
                )
            );
        }

        return $this->hydrateAggregateQuery($qa, $elementName, 'getContentTypeId');
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

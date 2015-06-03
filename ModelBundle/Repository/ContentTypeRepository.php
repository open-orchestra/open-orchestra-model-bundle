<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\PaginateAndSearchFilterTrait;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

/**
 * Class ContentTypeRepository
 */
class ContentTypeRepository extends AbstractRepository implements ContentTypeRepositoryInterface
{
    use PaginateAndSearchFilterTrait;

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
        $qa->group($this->generateLastVersionFilter($elementName));

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
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     * @param array|null  $order
     * @param int|null    $skip
     * @param int|null    $limit
     *
     * @return array
     */
    public function findAllByDeletedInLastVersionForPaginateAndSearch($descriptionEntity = null, $columns = null, $search = null, $order = null, $skip = null, $limit = null)
    {
        $qa = $this->createAggregateQueryByDeletedAndLastVersion();

        $qa = $this->generateFilterForSearch($qa, $descriptionEntity, $columns, $search);

        $elementName = 'contentType';
        $qa->group($this->generateLastVersionFilter($elementName));

        $qa = $this->generateFilterSort($qa, $order, $descriptionEntity, $columns, $elementName);

        $qa = $this->generateSkipFilter($qa, $skip);
        $qa = $this->generateLimitFilter($qa, $limit);

        return $this->hydrateAggregateQuery($qa, $elementName, 'getContentTypeId');
    }

    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     *
     * @return int
     */
    public function countByDeletedInLastVersionWithSearchFilter($descriptionEntity = null, $columns = null, $search = null)
    {
        $qa = $this->createAggregateQueryByDeletedAndLastVersion();
        $qa = $this->generateFilterForSearch($qa, $descriptionEntity, $columns, $search);

        $elementName = 'contentType';
        $qa->group($this->generateLastVersionFilter($elementName));

        return $this->countDocumentAggregateQuery($qa, $elementName);
    }

    /**
     * @return int
     */
    public function countByContentTypeInLastVersion()
    {
        $qa = $this->createAggregateQueryByDeletedAndLastVersion();
        $elementName = 'content';
        $qa->group($this->generateLastVersionFilter($elementName));

        return $this->countDocumentAggregateQuery($qa);
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

    /**
     * @param $elementName
     *
     * @return array
     */
    protected function generateLastVersionFilter($elementName)
    {
        return array(
            '_id' => array('contentTypeId' => '$contentTypeId'),
            'version' => array('$max' => '$version'),
            $elementName => array('$last' => '$$ROOT')
        );
    }

    /**
     * @return \Solution\MongoAggregation\Pipeline\Stage
     */
    protected function createAggregateQueryByDeletedAndLastVersion()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('deleted' => false));
        $qa->sort(array('contentTypeId' => -1));

        return $qa;
    }
}

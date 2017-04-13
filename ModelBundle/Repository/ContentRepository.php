<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\StatusableTrait;
use Solution\MongoAggregation\Pipeline\Stage;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\KeywordableTrait;
use OpenOrchestra\ModelInterface\Repository\RepositoryTrait\KeywordableTraitInterface;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\UseTrackableTrait;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\AutoPublishableTrait;
use OpenOrchestra\Pagination\MongoTrait\FilterTrait;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies\StringFilterStrategy;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies\BooleanFilterStrategy;
use OpenOrchestra\Pagination\MongoTrait\FilterTypeStrategy\Strategies\DateFilterStrategy;

/**
 * Class ContentRepository
 */
class ContentRepository extends AbstractAggregateRepository implements FieldAutoGenerableRepositoryInterface, ContentRepositoryInterface, KeywordableTraitInterface
{
    use KeywordableTrait;
    use UseTrackableTrait;
    use FilterTrait;
    use AutoPublishableTrait;
    use StatusableTrait;

    const ALIAS_FOR_GROUP = 'content';

    /**
     * @param string $contentId
     *
     * @return boolean
     */
    public function testUniquenessInContext($contentId)
    {
        return $this->findOneByContentId($contentId) !== null;
    }

    /**
     * @param string $contentId
     *
     * @return ContentInterface
     */
    public function findOneByContentId($contentId)
    {
        return $this->findOneBy(array('contentId' => $contentId));
    }

    /**
     * @param string $contentId
     * @param string $language
     *
     * @return ContentInterface
     */
    public function findPublishedVersion($contentId, $language)
    {
        $qa = $this->createAggregationQueryWithLanguageAndPublished($language);

        $qa->match(array('contentId' => $contentId));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string      $language
     * @param string      $contentType
     * @param string      $choiceType
     * @param string|null $condition
     * @param string|null $siteId
     *
     * @return array
     */
    public function findByContentTypeAndCondition($language, $contentType = '', $choiceType = self::CHOICE_AND, $condition = null, $siteId = null)
    {
        $qa = $this->createAggregationQuery();
        $qa->match($this->generateFilterPublishedNotDeletedOnLanguage($language));
        if (!is_null($siteId)) {
            $qa->match($this->generateSiteIdAndNotLinkedFilter($siteId));
        }
        $filter = $this->generateContentTypeFilter($contentType);

        if ($filter && $condition) {
            $qa->match($this->appendFilters($filter, $this->transformConditionToMongoCondition($condition), $choiceType));
        } elseif ($filter) {
            $qa->match($filter);
        } elseif ($condition) {
            $qa->match($this->transformConditionToMongoCondition($condition));
        }

        $qa = $this->generateLastVersionFilter($qa);

        return $this->hydrateAggregateQuery($qa, self::ALIAS_FOR_GROUP);
    }

    /**
     * Generate filter on visible published contents in $language
     *
     * @param string $language
     *
     * @return array
     */
    protected function generateFilterPublishedNotDeletedOnLanguage($language)
    {
        return array(
            'language' => $language,
            'deleted' => false,
            'status.publishedState' => true
        );
    }

    /**
     * Generate Content Type filter
     *
     * @param string|null $contentType
     *
     * @return array|null
     */
    protected function generateContentTypeFilter($contentType)
    {
        $filter = null;

        if (!is_null($contentType) && '' != $contentType) {
            $filter = array('contentType' => $contentType);
        }

        return $filter;
    }

    /**
     * Append two filters according to $choiceType operator
     *
     * @param array  $filter1
     * @param array  $filter2
     * @param string $choiceType
     *
     * @return array
     */
    protected function appendFilters($filter1, $filter2, $choiceType)
    {
        $choiceOperatior = '$and';
        if (self::CHOICE_OR == $choiceType) {
            $choiceOperatior = '$or';
        }

        return array($choiceOperatior => array($filter1, $filter2));
    }

    /**
     * @param string $contentId
     * @param string $language
     *
     * @return array
     */
    public function findNotDeletedSortByUpdatedAt($contentId, $language)
    {
        $qa = $this->createAggregationQueryWithLanguage($language);
        $qa->match(
            array(
                'contentId' => $contentId,
                'deleted'   => false,
            )
        );
        $qa->sort(array('updatedAt' => -1));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $contentId
     * @param string $language
     *
     * @return array
     */
    public function countNotDeletedByLanguage($contentId, $language)
    {
        $qa = $this->createAggregationQueryWithLanguage($language);
        $qa->match(
            array(
                'contentId' => $contentId,
                'deleted'   => false,
            )
        );

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string $contentId
     *
     * @return array
     */
    public function findByContentId($contentId)
    {
        return $this->findBy(array('contentId' => $contentId));
    }

    /**
     * @param string      $contentId
     * @param string      $language
     * @param string|null $version
     *
     * @return ContentInterface|null
     */
    public function findOneByLanguageAndVersion($contentId, $language, $version = null)
    {
        $qa = $this->createAggregationQueryWithContentIdAndLanguageAndVersion($contentId, $language, $version);

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $contentType
     * @param string                      $siteId
     * @param string                      $language
     * @param array                       $searchTypes
     *
     * @return array
     */
    public function findForPaginateFilterByContentTypeSiteAndLanguage(PaginateFinderConfiguration $configuration, $contentType, $siteId, $language, array $searchTypes = array())
    {
        $qa = $this->createAggregateQueryWithDeletedFilter(false);
        $qa->match($this->generateContentTypeFilter($contentType));
        $qa->match($this->generateSiteIdAndNotLinkedFilter($siteId));
        $qa->match($this->generateLanguageFilter($language));

        $this->filterSearch($configuration, $qa, $searchTypes);

        $order = $configuration->getOrder();
        $qa = $this->generateLastVersionFilter($qa, $order);

        $newOrder = array();
        array_walk($order, function($item, $key) use(&$newOrder) {
            $newOrder[str_replace('.', '_', $key)] = $item;
        });

        if (!empty($newOrder)) {
            $qa->sort($newOrder);
        }

        $qa->skip($configuration->getSkip());
        $qa->limit($configuration->getLimit());

        return $this->hydrateAggregateQuery($qa, self::ALIAS_FOR_GROUP);
    }


    /**
     * @param string $contentType
     * @param string $siteId
     * @param string $language
     *
     * @return int
     */
    public function countFilterByContentTypeSiteAndLanguage($contentType, $siteId, $language)
    {
        return $this->countInContextByContentTypeSiteAndLanguage($contentType, $siteId, $language);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param string                      $contentType
     * @param string                      $siteId
     * @param string                      $language
     * @param array                       $searchTypes
     *
     * @return int
     */
    public function countWithFilterAndContentTypeSiteAndLanguage(PaginateFinderConfiguration $configuration, $contentType, $siteId, $language, array $searchTypes = array())
    {
        return $this->countInContextByContentTypeSiteAndLanguage($contentType, $siteId, $language, $configuration, $searchTypes);
    }

    /**
     * @param string $contentType
     *
     * @return int
     */
    public function countByContentType($contentType)
    {
        $qa = $this->createAggregateQueryWithContentTypeFilter($contentType);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string       $id
     * @param string       $siteId
     * @param array|null   $eventTypes
     * @param boolean|null $published
     * @param int|null     $limit
     * @param array|null   $sort
     * @param array        $contentTypes
     *
     * @return array
     */
    public function findByHistoryAndSiteId(
        $id,
        $siteId,
        array $eventTypes = null,
        $published = null,
        $limit = null,
        array $sort = null,
        array $contentTypes = array()
    ) {
        $qa = $this->createAggregationQuery();
        $filter = array(
            'histories.user.$id' => new \MongoId($id),
            'deleted' => false
        );
        $qa->match($this->generateSiteIdAndNotLinkedFilter($siteId));
        if (null !== $eventTypes) {
            $filter['histories.eventType'] = array('$in' => $eventTypes);
        }
        if (null !== $published) {
            $filter['status.publishedState'] = $published;
        }
        if (!empty($contentTypes)) {
            $filter['contentType'] = array('$in' => $contentTypes);
        }

        $qa->match($filter);

        if (null !== $limit) {
            $qa->limit($limit);
        }

        if (null !== $sort) {
            $qa->sort($sort);
        }

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $entityId
     *
     * @return ContentInterface
     */
    public function findById($entityId)
    {
        return $this->find(new \MongoId($entityId));
    }

    /**
     * @param string $siteId
     *
     * @return array
     */
    protected function generateSiteIdAndNotLinkedFilter($siteId)
    {
        return array(
            '$or' => array(
                array('siteId' => $siteId),
                array('linkedToSite' => false)
            )
        );
    }

    /**
     * @param string $language
     *
     * @return array
     */
    protected function generateLanguageFilter($language)
    {
        return array('language' => $language);
    }

    /**
     * @param Stage $qa
     * @param array $order
     *
     * @return Stage
     */
    protected function generateLastVersionFilter(Stage $qa, array $order=array())
    {
        $group = array(
            '_id' => array('contentId' => '$contentId'),
            self::ALIAS_FOR_GROUP => array('$last' => '$$ROOT'),
        );

        foreach ($order as $column => $orderDirection) {
            $group[str_replace('.', '_', $column)] = array('$last' => '$' . $column);
        }

        $qa->sort(array('createdAt' => 1));
        $qa->group($group);

        return $qa;
    }

    /**
     * @param $contentType
     *
     * @return \Solution\MongoAggregation\Pipeline\Stage
     */
    protected function createAggregateQueryWithContentTypeFilter($contentType)
    {
        $qa = $this->createAggregationQuery();

        if ($contentType) {
            $qa->match(array('contentType' => $contentType));
        }

        return $qa;
    }

    /**
     * @param string $language
     *
     * @return Stage
     */
    protected function createAggregationQueryWithLanguage($language)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('language' => $language));

        return $qa;
    }

    /**
     * @param string      $contentId
     * @param string      $language
     * @param string|null $version
     *
     * @return Stage
     */
    protected function createAggregationQueryWithContentIdAndLanguageAndVersion($contentId, $language, $version = null)
    {
        $qa = $this->createAggregationQueryWithLanguage($language);
        $qa->match(
            array(
                'contentId' => $contentId
            )
        );
        if (is_null($version)) {
            $qa->sort(array('createdAt' => -1));
        } else {
            $qa->match(array('version' => $version));
        }

        return $qa;
    }

    /**
     * @param string $language
     *
     * @return Stage
     */
    protected function createAggregationQueryWithLanguageAndPublished($language)
    {
        $qa = $this->createAggregationQueryWithLanguage($language);
        $qa->match(
            array(
                'deleted'               => false,
                'status.publishedState' => true,
            )
        );

        return $qa;
    }

    /**
     * @param string $contentId
     * @param string $language
     * @param string $siteId
     *
     * @return ContentInterface
     */
    public function findOnePublished($contentId, $language, $siteId)
    {
        $qa = $this->createAggregationQueryWithLanguageAndPublished($language);
        $filter['contentId'] = $contentId;
        $qa->match($filter);

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string $contentId
     *
     * @return array
     */
    public function findAllPublishedByContentId($contentId)
    {
        $qa = $this->createAggregationQuery();
        $filter['status.publishedState'] = true;
        $filter['deleted'] = false;
        $filter['contentId'] = $contentId;
        $qa->match($filter);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param StatusableInterface $element
     *
     * @return array
     */
    public function findPublished(StatusableInterface $element)
    {
        $qa = $this->createAggregationQueryWithLanguageAndPublished($element->getLanguage());
        $qa->match(array('contentId' => $element->getContentId()));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param StatusInterface $status
     * @param string          $contentType
     *
     * @return array
     */
    public function updateStatusByContentType(StatusInterface $status, $contentType) {
        $this->createQueryBuilder()
            ->updateMany()
            ->field('status')->set($status)
            ->field('contentType')->equals($contentType)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $contentId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function softDeleteContent($contentId)
    {
        $qb = $this->createQueryBuilder();
        $qb->updateMany()
            ->field('contentId')->equals($contentId)
            ->field('deleted')->set(true)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $contentId
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function restoreDeletedContent($contentId)
    {
        $qb = $this->createQueryBuilder();
        $qb->updateMany()
            ->field('contentId')->equals($contentId)
            ->field('deleted')->set(false)
            ->getQuery()
            ->execute();
    }

    /**
     * @param array $ids
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function removeContentVersion(array $ids)
    {
        $contentMongoIds = array();
        foreach ($ids as $id) {
            $contentMongoIds[] = new \MongoId($id);
        }

        $qb = $this->createQueryBuilder();
        $qb->remove()
            ->field('id')->in($contentMongoIds)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $contentId
     *
     * @return ContentInterface
     */
    public function findLastVersion($contentId)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('deleted' => false));
        $qa->match(array('contentId' => $contentId));
        $qa->sort(array('createdAt' => -1));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string $contentId
     *
     * @return int
     */
    public function hasContentIdWithoutAutoUnpublishToState($contentId)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'contentId'  => $contentId,
                'status.autoUnpublishToState' => false
            )
        );

        return 0 !== $this->countDocumentAggregateQuery($qa);
    }


    public function findWithUseReferences($siteId)
    {
        $where = "function() { return this.useReferences && Object.keys(this.useReferences).length > 0; }";
        $qb = $this->createQueryBuilder();
        $qb->field('siteId')->equals($siteId)
            ->field('useReferences')->where($where);

        return $qb->getQuery()->execute();
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param Stage                       $qa
     * @param array                       $searchTypes
     *
     * @return Stage
     */
    protected function filterSearch(PaginateFinderConfiguration $configuration, Stage $qa, array $searchTypes)
    {
        $qa = $this->generateFilter($configuration, $qa, StringFilterStrategy::FILTER_TYPE, 'name', 'name');
        $language = $configuration->getSearchIndex('language');
        if (null !== $language && $language !== '') {
            $qa->match(array('language' => $language));
        }
        $status = $configuration->getSearchIndex('status');
        if (null !== $status && $status !== '') {
            $qa->match(array('status._id' => new \MongoId($status)));
        }
        $qa = $this->generateFilter($configuration, $qa, BooleanFilterStrategy::FILTER_TYPE, 'linked_to_site', 'linkedToSite');
        $qa = $this->generateFilter($configuration, $qa, DateFilterStrategy::FILTER_TYPE, 'created_at', 'createdAt', $configuration->getSearchIndex('date_format'));
        $qa = $this->generateFilter($configuration, $qa, StringFilterStrategy::FILTER_TYPE, 'created_by', 'createdBy');
        $qa = $this->generateFilter($configuration, $qa, DateFilterStrategy::FILTER_TYPE, 'updated_at', 'updatedAt', $configuration->getSearchIndex('date_format'));
        $qa = $this->generateFilter($configuration, $qa, StringFilterStrategy::FILTER_TYPE, 'updated_by', 'updatedBy');
        $qa = $this->generateFieldsFilter($configuration, $qa, $searchTypes);

        return $qa;
    }

    /**
     * @param $deleted
     *
     * @return \Solution\MongoAggregation\Pipeline\Stage
     */
    protected function createAggregateQueryWithDeletedFilter($deleted)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('deleted' => $deleted));

        return $qa;
    }

    /**
     * @param string                      $contentType
     * @param string                      $siteId
     * @param string                      $language
     * @param array                       $searchTypes
     * @param PaginateFinderConfiguration $configuration
     *
     * @return int
     */
    protected function countInContextByContentTypeSiteAndLanguage($contentType, $siteId, $language, PaginateFinderConfiguration $configuration = null, array $searchTypes = array())
    {
        $qa = $this->createAggregateQueryWithDeletedFilter(false);
        $qa->match($this->generateContentTypeFilter($contentType));
        $qa->match($this->generateSiteIdAndNotLinkedFilter($siteId));
        $qa->match($this->generateLanguageFilter($language));

        if (!is_null($configuration)) {
            $this->filterSearch($configuration, $qa, $searchTypes);
        }

        $qa = $this->generateLastVersionFilter($qa);

        return $this->countDocumentAggregateQuery($qa, self::ALIAS_FOR_GROUP);
    }
}

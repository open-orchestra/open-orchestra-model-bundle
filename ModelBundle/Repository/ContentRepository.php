<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use Solution\MongoAggregation\Pipeline\Stage;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\KeywordableTrait;
use OpenOrchestra\ModelInterface\Repository\RepositoryTrait\KeywordableTraitInterface;

/**
 * Class ContentRepository
 */
class ContentRepository extends AbstractAggregateRepository implements FieldAutoGenerableRepositoryInterface, ContentRepositoryInterface, KeywordableTraitInterface
{
    use PaginationTrait;
    use KeywordableTrait;

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
    public function findLastPublishedVersion($contentId, $language)
    {
        $qa = $this->createAggregationQueryWithLanguageAndPublished($language);

        $qa->match(array('contentId' => $contentId));
        $qa->sort(array('version' => -1));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string      $language
     * @param string      $contentType
     * @param string      $choiceType
     * @param string|null $keywords
     *
     * @deprecated will be removed in 1.3.0
     *
     * @return array
     */
    public function findByContentTypeAndKeywords($language, $contentType = '', $choiceType = self::CHOICE_AND, $keywords = null)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 1.3.0', E_USER_DEPRECATED);

        $qa = $this->createAggregationQuery();
        $qa->match($this->generateFilterPublishedNotDeletedOnLanguage($language));

        $filter1 = $this->generateContentTypeFilter($contentType);
        $filter2 = $this->generateKeywordsFilter($keywords);

        if ($filter1 && $filter2) {
            $qa->match($this->appendFilters($filter1, $filter2, $choiceType));
        } elseif ($filter1) {
            $qa->match($filter1);
        } elseif ($filter2) {
            $qa->match($filter2);
        }

        $elementName = 'content';

        $this->generateLastVersionFilter($qa, $elementName);

        return $this->hydrateAggregateQuery($qa, $elementName);
    }

    /**
     * @param string      $language
     * @param string      $contentType
     * @param string      $choiceType
     * @param string|null $condition
     *
     * @return array
     */
    public function findByContentTypeAndCondition($language, $contentType = '', $choiceType = self::CHOICE_AND, $condition = null)
    {
        $qa = $this->createAggregationQuery();
        $qa->match($this->generateFilterPublishedNotDeletedOnLanguage($language));

        $filter = $this->generateContentTypeFilter($contentType);

        if ($filter && $condition) {
            $qa->match($this->appendFilters($filter, $this->transformConditionToMongoCondition($condition), $choiceType));
        } elseif ($filter) {
            $qa->match($filter);
        } elseif ($condition) {
            $qa->match($this->transformConditionToMongoCondition($condition));
        }

        $elementName = 'content';

        $this->generateLastVersionFilter($qa, $elementName);

        return $this->hydrateAggregateQuery($qa, $elementName);
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
            '$and' => array(
                'language' => $language,
                'deleted' => false,
                '$or' => array(
                    'status.published' => true,
                    'statusable' => false,
                ),
             )
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
     * Generate keywords filter
     *
     * @param string $keywords
     *
     * @return array|null
     */
    protected function generateKeywordsFilter($keywords)
    {
        $filter = null;

        if (!is_null($keywords) && '' !== $keywords) {
            $keywordFilters = array();

            $keywords = explode(',', $keywords);
            foreach ($keywords as $keyword) {
                $keywordFilters[] = array('keywords.label' => $keyword);
            }

            $filter = array('$and' => $keywordFilters);
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
     * @return ContentInterface|null
     */
    public function findOneByLanguage($contentId, $language)
    {
        return $this->findOneByLanguageAndVersion($contentId, $language, null);
    }

    /**
     * @param string $contentId
     * @param string $language
     *
     * @return array
     */
    public function findByLanguage($contentId, $language)
    {
        $qa = $this->createAggregationQueryWithContentIdAndLanguageAndVersion($contentId, $language, null);

        return $this->hydrateAggregateQuery($qa);
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
     * @param int|null    $version
     *
     * @return ContentInterface|null
     */
    public function findOneByLanguageAndVersion($contentId, $language, $version = null)
    {
        $qa = $this->createAggregationQueryWithContentIdAndLanguageAndVersion($contentId, $language, $version);

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string|null                 $contentType
     * @param PaginateFinderConfiguration $configuration
     * @param string|null                 $siteId
     *
     * @return array
     */
    public function findPaginatedLastVersionByContentTypeAndSite($contentType = null, PaginateFinderConfiguration $configuration = null, $siteId = null)
    {
        $qa = $this->createAggregateQueryWithContentTypeFilter($contentType);
        $qa = $this->generateFilter($qa, $configuration);
        $qa->match($this->generateDeletedFilter());
        if (!is_null($siteId)) {
            $qa->match($this->generateSiteIdAndNotLinkedFilter($siteId));
        }

        $elementName = 'content';
        $this->generateLastVersionFilter($qa, $elementName, $configuration);

        $qa = $this->generateFilterSort(
            $qa,
            $configuration->getOrder(),
            $configuration->getDescriptionEntity(),
            $elementName);

        $qa = $this->generateSkipFilter($qa, $configuration->getSkip());
        $qa = $this->generateLimitFilter($qa, $configuration->getLimit());

        return $this->hydrateAggregateQuery($qa, $elementName);
    }

    /**
     * @param string|null         $contentType
     * @param FinderConfiguration $configuration
     * @param int|null            $siteId
     *
     * @return int
     */
    public function countByContentTypeInLastVersionWithFilter(
        $contentType,
        FinderConfiguration $configuration = null,
        $siteId = null
    ) {
        $qa = $this->createAggregateQueryWithContentTypeFilter($contentType);
        $qa = $this->generateFilter($qa, $configuration);
        $qa->match($this->generateDeletedFilter());
        if (!is_null($siteId)) {
            $qa->match($this->generateSiteIdAndNotLinkedFilter($siteId));
        }
        $this->generateLastVersionFilter($qa, 'content');
        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string|null $contentType
     *
     * @return int
     *
     * @deprecated will be removed in 2.0, use countByContentTypeAndSiteInLastVersion
     */
    public function countByContentTypeInLastVersion($contentType = null)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.1.3 and will be removed in 2.0. Use the '.__CLASS__.'::countByContentTypeAndSiteInLastVersion method instead.', E_USER_DEPRECATED);
        $qa = $this->createAggregateQueryWithContentTypeFilter($contentType);
        $qa->match($this->generateDeletedFilter());
        $elementName = 'content';
        $this->generateLastVersionFilter($qa, $elementName);
        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string      $contentType
     * @param string|null $siteId
     *
     * @return int
     */
    public function countByContentTypeAndSiteInLastVersion($contentType, $siteId = null)
    {
        $qa = $this->createAggregateQueryWithContentTypeFilter($contentType);
        $qa->match($this->generateDeletedFilter());
        if (!is_null($siteId)) {
            $qa->match($this->generateSiteIdAndNotLinkedFilter($siteId));
        }
        $this->generateLastVersionFilter($qa, 'content');
        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string       $author
     * @param string       $siteId
     * @param boolean|null $published
     * @param int|null     $limit
     * @param array        $sort
     *
     * @return array
     */
    public function findByAuthorAndSiteId($author, $siteId, $published = null, $limit = null, $sort = null)
    {
        $qa = $this->createAggregationQuery();
        $filter = array(
            'createdBy' => $author,
            'deleted' => false
        );
        $qa->match($this->generateSiteIdAndNotLinkedFilter($siteId));
        if (null !== $published) {
            $filter = array(
                '$and' => array(
                    'createdBy' => $author,
                    'deleted' => false,
                    'status.published' => $published,
                )
            );
            if ($published) {
                $filter = array(
                    '$and' => array(
                        'createdBy' => $author,
                        'deleted' => false,
                        '$or' => array(
                            'status.published' => true,
                            'statusable' => false,
                        ),
                    )
                );
            }
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
     * @return array
     */
    protected function generateDeletedFilter()
    {
        return array('deleted' => false);
    }

    /**
     * @param Stage                            $qa
     * @param string                           $elementName
     * @param PaginateFinderConfiguration|null $configuration
     */
    protected function generateLastVersionFilter(Stage $qa, $elementName, $configuration = null)
    {
        $group = array();

        if (!is_null($configuration)) {
            $group = $this->generateGroupForFilterSort($configuration);
        }
        $group = array_merge($group,
            array(
                '_id' => array('contentId' => '$contentId'),
                $elementName => array('$last' => '$$ROOT')
        ));

        $qa->sort(array('version' => 1));
        $qa->group($group);
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
     * @param int|null    $version
     *
     * @return Stage
     */
    protected function createAggregationQueryWithContentIdAndLanguageAndVersion($contentId, $language, $version = null)
    {
        $qa = $this->createAggregationQueryWithLanguage($language);
        $qa->match(
            array(
                'contentId' => $contentId,
                'deleted'   => false,
            )
        );
        if (is_null($version)) {
            $qa->sort(array('version' => -1));
        } else {
            $qa->match(array('version' => (int) $version));
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
                '$and' => array(
                    'deleted' => false,
                    '$or' => array(
                        'status.published' => true,
                        'statusable' => false,
                    )
                )
            )
        );

        return $qa;
    }

    /**
     * @param StatusInterface $status
     *
     * @return bool
     */
    public function hasStatusedElement(StatusInterface $status)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('status._id' => new \MongoId($status->getId())));
        $content = $this->singleHydrateAggregateQuery($qa);

        return $content instanceof ContentInterface;
    }

    /**
     * @param string $contentId
     * @param string $language
     * @param string $siteId
     *
     * @return ContentInterface
     */
    public function findOneCurrentlyPublished($contentId, $language, $siteId)
    {
        $qa = $this->createAggregationQueryWithLanguageAndPublished($language);
        $filter['currentlyPublished'] = true;
        $filter['deleted'] = false;
        $filter['contentId'] = $contentId;
        $qa->match($filter);
        $qa->sort(array('version' => -1));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param ContentInterface $element
     *
     * @return StatusableInterface
     */
    public function findOneCurrentlyPublishedByElement(StatusableInterface $element)
    {
        return $this->findOneCurrentlyPublished($element->getContentId(), $element->getLanguage(), $element->getSiteId());
    }

    /**
     * @param ContentInterface $element
     *
     * @return ContentInterface
     */
    public function findPublishedInLastVersionWithoutFlag(StatusableInterface $element)
    {
        $qa = $this->createAggregationQueryWithLanguageAndPublished($element->getLanguage());
        $filter = array(
            '$and' => array(
                'deleted' => false,
                'contentId'=> $element->getContentId(),
                '$or' => array(
                    'statusable' => false,
                    '$and' => array(
                        'status.published' => true,
                        'currentlyPublished' => false
                    )
                )
            )
        )
        $qa->match($filter);
        $qa->sort(array('version' => -1));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param ContentInterface $element
     *
     * @return array
     */
    public function findAllCurrentlyPublishedByElementId(StatusableInterface $element)
    {
        return $this->findBy(array(
            'contentId' => $element->getContentId(),
            'language' => $element->getLanguage(),
            'currentlyPublished' => true
        ));
    }
}

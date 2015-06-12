<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\PaginateAndSearchFilterTrait;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class ContentRepository
 */
class ContentRepository extends AbstractRepository implements FieldAutoGenerableRepositoryInterface, ContentRepositoryInterface
{
    use PaginateAndSearchFilterTrait;

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
    public function findLastPublishedVersionByContentIdAndLanguage($contentId, $language)
    {
        $qa = $this->createAggregationQueryWithLanguageAndPublished($language);

        $qa->match(array('contentId' => $contentId));
        $qa->sort(array('version' => -1));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string $language
     * @param string $contentType
     * @param string $choiceType
     * @param string $keywords
     *
     * @return array
     */
    public function findByContentTypeAndChoiceTypeAndKeywordsAndLanguage($language, $contentType = '', $choiceType = self::CHOICE_AND, $keywords = null)
    {
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
        $qa->group(array(
            '_id' => array('contentId' => '$contentId'),
            'version' => array('$max' => '$version'),
            $elementName => array('$last' => '$$ROOT')
        ));

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
            'language' => $language,
            'deleted' => false,
            'status.published' => true
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
        if (self::CHOICE_OR == $choiceType) {
            return array('$or' => array($filter1, $filter2));
        } else {
            return array('$and' => array($filter1, $filter2));
        }
    }

    /**
     * @param string $contentId
     * @param string $language
     *
     * @return ContentInterface|null
     */
    public function findOneByContentIdAndLanguage($contentId, $language)
    {
        return $this->findOneByContentIdAndLanguageAndVersion($contentId, $language, null);
    }

    /**
     * @param string $contentId
     * @param string $language
     *
     * @return array
     */
    public function findByContentIdAndLanguage($contentId, $language)
    {
        $qa = $this->createAggregationQueryWithContentIdAndLanguageAndVersion($contentId, $language, null);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string      $contentId
     * @param string      $language
     * @param int|null    $version
     *
     * @return ContentInterface|null
     */
    public function findOneByContentIdAndLanguageAndVersion($contentId, $language, $version = null)
    {
        $qa = $this->createAggregationQueryWithContentIdAndLanguageAndVersion($contentId, $language, $version);

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @deprecated use findByContentTypeInLastVersionForPaginateAndSearch
     *
     * @param string $contentType
     *
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findByContentTypeInLastVersion($contentType = null)
    {

        $qa = $this->createAggregateQueryWithContentTypeFilter($contentType);
        $qa->match($this->generateDeletedFilter());
        $elementName = 'content';
        $qa->group($this->generateLastVersionFilter($elementName));

        return $this->hydrateAggregateQuery($qa, $elementName);
    }

    /**
     * @param string|null $contentType
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     * @param array|null  $order
     * @param int|null    $skip
     * @param int|null    $limit
     *
     * @deprecated, use findByContentTypeInLastVersionForPaginateAndSearchAndSiteId instead
     *
     * @return array
     */
    public function findByContentTypeInLastVersionForPaginateAndSearch($contentType = null, $descriptionEntity = null, $columns = null, $search = null, $order = null, $skip = null, $limit = null)
    {
        return $this->findByContentTypeInLastVersionForPaginateAndSearchAndSiteId(
            $contentType,
            $descriptionEntity,
            $columns,
            $search,
            null,
            $order,
            $skip,
            $limit
        );
    }

    /**
     * @param string|null $contentType
     * @param array|null $descriptionEntity
     * @param array|null $columns
     * @param string|null $search
     * @param string|null $siteId
     * @param array|null $order
     * @param int|null $skip
     * @param int|null $limit
     *
     * @return array
     */
    public function findByContentTypeInLastVersionForPaginateAndSearchAndSiteId($contentType = null, $descriptionEntity = null, $columns = null, $search = null, $siteId = null, $order = null, $skip = null, $limit = null)
    {
        $qa = $this->createAggregateQueryWithContentTypeFilter($contentType);
        $qa = $this->generateFilterForSearch($qa, $descriptionEntity, $columns, $search);
        $qa->match($this->generateDeletedFilter());
        if (!is_null($siteId)) {
            $qa->match(array('$or' => array(array('siteId' => $siteId), array('linkedToSite' => false))));
        }

        $elementName = 'content';
        $qa->group($this->generateLastVersionFilter($elementName));

        $qa = $this->generateFilterSort($qa, $order, $descriptionEntity, $columns, $elementName);

        $qa = $this->generateSkipFilter($qa, $skip);
        $qa = $this->generateLimitFilter($qa, $limit);

        return $this->hydrateAggregateQuery($qa, $elementName);
    }

    /**
     * @param string|null $contentType
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     *
     * @return int
     */
    public function countByContentTypeInLastVersionWithSearchFilter($contentType = null, $descriptionEntity = null, $columns = null, $search = null)
    {
        $qa = $this->createAggregateQueryWithContentTypeFilter($contentType);
        $qa = $this->generateFilterForSearch($qa, $descriptionEntity, $columns, $search);
        $qa->match($this->generateDeletedFilter());
        $elementName = 'content';
        $qa->group($this->generateLastVersionFilter($elementName));

        return $this->countDocumentAggregateQuery($qa, $elementName);
    }

    /**
     * @param string|null $contentType
     *
     * @return int
     */
    public function countByContentTypeInLastVersion($contentType = null)
    {
        $qa = $this->createAggregateQueryWithContentTypeFilter($contentType);
        $qa->match($this->generateDeletedFilter());
        $elementName = 'content';
        $qa->group($this->generateLastVersionFilter($elementName));

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @return array
     */
    public function findAllDeleted()
    {
        return parent::findBy(array('deleted' => true));
    }

    /**
     * @return array
     */
    protected function generateDeletedFilter()
    {
        return array('deleted' => false);
    }

    /**
     * @param string $elementName
     *
     * @return array
     */
    protected function generateLastVersionFilter($elementName)
    {
        return array(
            '_id' => array('contentId' => '$contentId'),
            'version' => array('$max' => '$version'),
            $elementName => array('$last' => '$$ROOT')
        );
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
                'deleted'          => false,
                'status.published' => true,
            )
        );

        return $qa;
    }
}

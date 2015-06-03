<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\PaginateAndSearchFilterTrait;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Doctrine\ODM\MongoDB\Query\Builder;

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
    public function testUnicityInContext($contentId)
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
        $qb = $this->createQueryWithLanguageAndPublished($language);

        $qb->field('contentId')->equals($contentId);
        $qb->sort('version', 'desc');

        return $qb->getQuery()->getSingleResult();
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
        $qb = $this->createQueryWithContentIdAndLanguageAndVersion($contentId, $language, null);

        return $qb->getQuery()->execute();
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
        $qb = $this->createQueryWithContentIdAndLanguageAndVersion($contentId, $language, $version);

        return $qb->getQuery()->getSingleResult();
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
        $qa = $this->createAggregateQueryWithContentTypeFiler($contentType);
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
     * @return array
     */
    public function findByContentTypeInLastVersionForPaginateAndSearch($contentType = null, $descriptionEntity = null, $columns = null, $search = null, $order = null, $skip = null, $limit = null)
    {
        $qa = $this->createAggregateQueryWithContentTypeFiler($contentType);
        $qa = $this->generateFilterForSearch($qa, $descriptionEntity, $columns, $search);

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
    public function countByContentTypeInLastVersionFilterSearch($contentType = null, $descriptionEntity = null, $columns = null, $search = null)
    {
        $qa = $this->createAggregateQueryWithContentTypeFiler($contentType);
        $qa = $this->generateFilterForSearch($qa, $descriptionEntity, $columns, $search);

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
        $qa = $this->createAggregateQueryWithContentTypeFiler($contentType);
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
    protected function createAggregateQueryWithContentTypeFiler($contentType)
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
     * @return Builder
     */
    protected function createQueryWithLanguage($language)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->field('language')->equals($language);

        return $qb;
    }

    /**
     * @param string      $contentId
     * @param string      $language
     * @param int|null    $version
     *
     * @return Builder
     */
    protected function createQueryWithContentIdAndLanguageAndVersion($contentId, $language, $version = null)
    {
        $qb = $this->createQueryWithLanguage($language);

        $qb->field('contentId')->equals($contentId);
        $qb->field('deleted')->equals(false);

        if (is_null($version)) {
            $qb->sort('version', 'desc');
        } else {
            $qb->field('version')->equals((int) $version);
        }

        return $qb;
    }

    /**
     * @param string $language
     *
     * @return Builder
     */
    protected function createQueryWithLanguageAndPublished($language)
    {
        $qb = $this->createQueryWithLanguage($language);

        $qb->field('deleted')->equals(false);
        $qb->field('status.published')->equals(true);

        return $qb;
    }
}

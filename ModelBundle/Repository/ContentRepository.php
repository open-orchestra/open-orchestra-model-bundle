<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class ContentRepository
 */
class ContentRepository extends AbstractRepository implements FieldAutoGenerableRepositoryInterface, ContentRepositoryInterface
{
    /**
     * Get all content if the contentType is "news"
     *
     * @deprecated This will be removed in the 0.2.1 version
     *
     * @return array list of news
     */
    public function findAllNews()
    {
        $criteria = array(
            'contentType'=> "news",
            'status'=> "published"
        );

        return $this->findBy($criteria);
    }

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
     * @deprecated use findByContentTypeAndChoiceTypeAndKeywordsAndLanguage
     *
     * @param string $contentType
     * @param string $choiceType
     * @param string $keywords
     *
     * @return array
     */
    public function findByContentTypeAndChoiceTypeAndKeywords($contentType = '', $choiceType = self::CHOICE_AND, $keywords = null)
    {
        $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
        $qb = $this->createQueryFindByContentTypeAndChoiceTypeAndKeywordsAndLanguage($language, $contentType, $choiceType, $keywords);

        return $this->findLastVersion($qb);
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
        $qb = $this->createQueryFindByContentTypeAndChoiceTypeAndKeywordsAndLanguage($language, $contentType, $choiceType, $keywords);

        return $this->findLastVersion($qb);
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
        $qb = $this->createQueryWithDefaultCriteria($contentId, $language, null);

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
        $qb = $this->createQueryWithDefaultCriteria($contentId, $language, $version);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param string $contentType
     *
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findByContentTypeInLastVersion($contentType = null)
    {
        $qa = $this->createAggregationQuery();

        if ($contentType) {
            $qa->match(array('contentType' => $contentType));
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
     * @return array
     */
    public function findAllDeleted()
    {
        return parent::findBy(array('deleted' => true));
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
    protected function createQueryWithDefaultCriteria($contentId, $language, $version = null)
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

    /**
     * @return array
     */
    protected function findLastVersion(Builder $qb)
    {
        $qb->sort('version', 'desc');
        $list = $qb->getQuery()->execute();

        $contents = array();

        foreach ($list as $content) {
            if (empty($contents[$content->getContentId()])) {
                $contents[$content->getContentId()] = $content;
            }
        }

        return $contents;
    }

    /**
     * @param string $language
     * @param string $contentType
     * @param string $choiceType
     * @param string $keywords
     *
     * @return Builder
     */
    protected function createQueryFindByContentTypeAndChoiceTypeAndKeywordsAndLanguage($language, $contentType, $choiceType, $keywords)
    {
        $qb = $this->createQueryWithLanguageAndPublished($language);

        $addMethod = 'addAnd';
        if ($choiceType == self::CHOICE_OR) {
            $addMethod = 'addOr';
        }

        if (!empty($keywords)) {
            $qb->$addMethod($qb->expr()->field('keywords.label')->in(explode(',', $keywords)));
        }
        if ('' !== $contentType) {
            $qb->$addMethod($qb->expr()->field('contentType')->equals($contentType));
            return $qb;
        }

        return $qb;
    }
}

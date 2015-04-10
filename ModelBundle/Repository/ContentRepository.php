<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelBundle\Repository\FieldAutoGenerableRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Class ContentRepository
 */
class ContentRepository extends DocumentRepository implements FieldAutoGenerableRepositoryInterface, ContentRepositoryInterface
{
    /**
     * @var CurrentSiteIdInterface
     */
    protected $currentSiteManager;

    /**
     * @param CurrentSiteIdInterface $currentSiteManager
     */
    public function setCurrentSiteManager(CurrentSiteIdInterface $currentSiteManager)
    {
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * Get all content if the contentType is "news"
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
     * @param string $name
     *
     * @return boolean
     */
    public function testUnicityInContext($name)
    {
        return $this->findOneByName($name) !== null;
    }

    /**
     * @param string $contentId
     *
     * @return ContentInterface
     */
    public function findOneByContentId($contentId)
    {
      
        $qb = $this->createQueryWithLanguageAndPublished();

        $qb->field('contentId')->equals($contentId);
        $qb->sort('version', 'desc');

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param string $contentType
     * @param string $choiceType
     * @param string $keywords
     *
     * @return array
     */
    public function findByContentTypeAndChoiceTypeAndKeywords($contentType = '', $choiceType = self::CHOICE_AND, $keywords = null)
    {
        $qb = $this->createQueryWithLanguageAndPublished();
        $qb = $this->getQueryFindByContentTypeAndChoiceTypeAndKeywords($qb, $contentType, $choiceType, $keywords);

        return $this->findLastVersion($qb);
    }

    /**
     * @param string      $contentId
     * @param string|null $language
     *
     * @return ContentInterface|null
     */
    public function findOneByContentIdAndLanguage($contentId, $language = null)
    {
        return $this->findOneByContentIdAndLanguageAndVersion($contentId, $language, null);
    }

    /**
     * @param string      $contentId
     * @param string|null $language
     *
     * @return array
     */
    public function findByContentIdAndLanguage($contentId, $language = null)
    {
        $qb = $this->createQueryWithDefaultCriteria($contentId, $language, null);

        return $qb->getQuery()->execute();
    }

    /**
     * @param string      $contentId
     * @param string|null $language
     * @param int|null    $version
     *
     * @return ContentInterface|null
     */
    public function findOneByContentIdAndLanguageAndVersion($contentId, $language = null, $version = null)
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
        $qb = $this->createQueryBuilder('c');
        if ($contentType) {
            $qb->field('contentType')->equals($contentType);
        }
        $qb->field('deleted')->equals(false);

        return $this->findLastVersion($qb);
    }

    /**
     * @return array
     */
    public function findAllDeleted()
    {
        return parent::findBy(array('deleted' => true));
    }

    /**
     * @param string|null $languageAndPublishedQueryCriteria
     *
     * @return Builder
     */
    protected function createQueryWithLanguage($language = null)
    {
        $qb = $this->createQueryBuilder('c');

        if (is_null($language)) {
            $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
        }

        $qb->field('language')->equals($language);

        return $qb;
    }

    /**
     * @param string      $contentId
     * @param string|null $language
     * @param int|null    $version
     *
     * @return Builder
     */
    protected function createQueryWithDefaultCriteria($contentId, $language = null, $version = null)
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
     * @param string|null $language
     *
     * @return Builder
     */
    protected function createQueryWithLanguageAndPublished($language = null)
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
     * @param $contentType
     * @param $choiceType
     * @param $keywords
     * @return Builder
     */
    protected function getQueryFindByContentTypeAndChoiceTypeAndKeywords(Builder $qb, $contentType, $choiceType, $keywords)
    {
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

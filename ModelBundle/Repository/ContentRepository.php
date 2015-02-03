<?php

namespace PHPOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use PHPOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use PHPOrchestra\ModelBundle\Repository\FieldAutoGenerableRepositoryInterface;
use PHPOrchestra\ModelInterface\Model\ContentInterface;
use PHPOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @param string      $contentId
     * @param string|null $language
     * @param int|null    $version
     *
     * @return Builder
     */
    protected function defaultQueryCriteria(Builder $qb, $contentId, $language = null, $version = null)
    {
        if (is_null($language)) {
            $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
        }
        $qb->field('contentId')->equals($contentId);
        $qb->field('language')->equals($language);
        $qb->field('deleted')->equals(false);
        if (is_null($version)) {
            $qb->sort('version', 'desc');
        } else {
            $qb->field('version')->equals((int) $version);
        }

        return $qb;
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
        return $this->findOneBy(array('contentId' => $contentId));
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
        $qb = $this->getQueryFindByContentTypeAndChoiceTypeAndKeywords($contentType, $choiceType, $keywords);

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $contentType
     * @param string $choiceType
     * @param string $keywords
     *
     * @return array
     */
    public function findByContentTypeAndChoiceTypeAndKeywordsNotHydrated($contentType = '', $choiceType = self::CHOICE_AND, $keywords = null)
    {
        $qb = $this->getQueryFindByContentTypeAndChoiceTypeAndKeywords($contentType, $choiceType, $keywords);

        return $qb->hydrate(false)->getQuery()->execute();
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
        $qb = $this->createQueryBuilder('c');
        $qb = $this->defaultQueryCriteria($qb, $contentId, $language, null);

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
        $qb = $this->createQueryBuilder('c');
        $qb = $this->defaultQueryCriteria($qb, $contentId, $language, $version);

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
        $qb->sort('version', 'desc');

        $keys = array("contentId" => 1);
        $initial = array("first" => true, 'content' => array());
        $reduce = "function (obj, prev) { if (prev.rank) prev.content = obj; prev.rank = false;}";
        $qb->group($keys, $initial, $reduce);

        $contents = array_map(create_function('$content', 'return $content["content"];'), $qb->getQuery()->execute()->toArray());

        return new ArrayCollection($contents);
    }

    /**
     * @return array
     */
    public function findAllDeleted()
    {
        return parent::findBy(array('deleted' => true));
    }

    /**
     * @param $contentType
     * @param $choiceType
     * @param $keywords
     * @return Builder
     */
    protected function getQueryFindByContentTypeAndChoiceTypeAndKeywords($contentType, $choiceType, $keywords)
    {
        $qb = $this->createQueryBuilder('c');

        $addMethod = 'addAnd';
        if ($choiceType == self::CHOICE_OR) {
            $addMethod = 'addOr';
        }

        if (!is_null($keywords)) {
            $qb->$addMethod($qb->expr()->field('keywords.label')->in(explode(',', $keywords)));
        }
        if ('' !== $contentType) {
            $qb->$addMethod($qb->expr()->field('contentType')->equals($contentType));
            return $qb;
        }
        return $qb;
    }
}

<?php

namespace PHPOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use PHPOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use PHPOrchestra\ModelBundle\Repository\FieldAutoGenerableRepositoryInterface;
use PHPOrchestra\ModelInterface\Model\ContentInterface;
use PHPOrchestra\ModelInterface\Repository\ContentRepositoryInterface;

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
        return $this->findOneBy(array('contentId' => $contentId));
    }

    /**
     * @param string      $contentType
     * @param string|null $keywords
     *
     * @return mixed
     */
    public function findByContentTypeAndKeywords($contentType = '', $keywords = null)
    {
        $qb = $this->createQueryBuilder('c');

        if (!is_null($keywords)) {
            $qb->field('keywords.label')->in(explode(',', $keywords));
        }

        if ('' !== $contentType) {
            $qb->field('contentType')->equals($contentType);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $contentId
     * @param string $language
     *
     * @return mixed
     */
    public function findOneByContentIdAndLanguage($contentId, $language)
    {
        return $this->findOneByContentIdAndLanguageAndVersion($contentId, $language, null);
    }

    /**
     * @param string      $contentId
     * @param string|null $language
     *
     * @return mixed
     */
    public function findByContentIdAndLanguage($contentId, $language = null)
    {
        return $this->findByContentIdAndLanguageAndVersion($contentId, $language, false);
    }

    /**
     * @param string      $contentId
     * @param string|null $language
     * @param int|null    $version
     *
     * @return mixed
     */
    public function findOneByContentIdAndLanguageAndVersion($contentId, $language = null, $version = null)
    {
        return $this->findByContentIdAndLanguageAndVersion($contentId, $language, $version);
    }

    /**
     * @param string      $contentId
     * @param string|null $language
     * @param int|null    $version
     *
     * @return mixed
     */
    public function findByContentIdAndLanguageAndVersion($contentId, $language = null, $version = false)
    {
        if (is_null($language)) {
            $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
        }
        $qb = $this->createQueryBuilder('c');
        $qb->field('contentId')->equals($contentId);
        $qb->field('language')->equals($language);
        $qb->field('deleted')->equals(false);

        if ($version === false) {
            $qb->sort('version', 'desc');
            return $qb->getQuery()->execute();
        }

        if (is_null($version)) {
            $qb->sort('version', 'desc');
            return $qb->getQuery()->getSingleResult();
        }

        $qb->field('version')->equals((int) $version);
        return $qb->getQuery()->getSingleResult();
    }
}

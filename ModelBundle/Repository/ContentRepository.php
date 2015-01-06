<?php

namespace PHPOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use PHPOrchestra\ModelBundle\Repository\FieldAutoGenerableRepositoryInterface;
use PHPOrchestra\ModelInterface\Model\ContentInterface;
use PHPOrchestra\ModelInterface\Repository\ContentRepositoryInterface;

/**
 * Class ContentRepository
 */
class ContentRepository extends DocumentRepository implements FieldAutoGenerableRepositoryInterface, ContentRepositoryInterface
{
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
     * @return ContentInterface
     */
    public function findOneByContentIdAndLanguage($contentId, $language)
    {
        return $this->findOneBy(array('contentId' => $contentId, 'language' => $language));
    }
}

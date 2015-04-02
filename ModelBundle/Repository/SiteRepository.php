<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;

/**
 * Class SiteRepository
 */
class SiteRepository extends DocumentRepository implements SiteRepositoryInterface
{
    /**
     * @param string $siteId
     *
     * @return SiteInterface
     */
    public function findOneBySiteId($siteId)
    {
        return $this->findOneBy(array('siteId' => $siteId));
    }

    /**
     * @param $siteId
     * 
     * @return SiteInterface
     */
    public function findOneBySiteIdNotDeleted($siteId)
    {
        return $this->findOneBy(array('siteId' => $siteId, 'deleted' => false));
    }

    /**
     * @param boolean $deleted
     *
     * @return array
     */
    public function findByDeleted($deleted)
    {
        return $this->findBy(array('deleted' => $deleted));
    }

    /**
     * @param string $domain
     *
     * @return ReadSiteInterface
     */
    public function findByAliasDomain($domain)
    {
        $qb = $this->createQueryBuilder();
        $qb->field('aliases.domain')->equals($domain);

        return $qb->getQuery()->getSingleResult();
    }
}

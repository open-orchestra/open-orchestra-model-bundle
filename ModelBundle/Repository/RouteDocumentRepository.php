<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ModelInterface\Model\RouteDocumentInterface;
use OpenOrchestra\ModelInterface\Repository\RouteDocumentRepositoryInterface;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class RouteDocumentRepository
 */
class RouteDocumentRepository extends AbstractAggregateRepository implements RouteDocumentRepositoryInterface
{
    /**
     * @param string $name
     *
     * @return RouteDocumentInterface
     */
    public function findOneByName($name)
    {
        return $this->findOneBy(array('name' => $name));
    }

    /**
     * @param string $redirectionId
     *
     * @return RouteDocumentInterface
     */
    public function removeByRedirectionId($redirectionId)
    {
        $this->createQueryBuilder()
            ->remove()
            ->field('name')->equals(new \MongoRegex('/.*_' . $redirectionId . '/'))
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $pathInfo
     *
     * @return Collection
     */
    public function findByPathInfo($pathInfo)
    {
        $workingPattern = explode('/', trim($pathInfo, '/'));

        $qa = $this->createAggregationQuery('r');

        $filter = array();
        $i = 0;
        while ($i <= 10) {
            if (array_key_exists($i, $workingPattern)) {
                $filter['token' . $i] = new \MongoRegex('/^' . $workingPattern[$i] . '$|\*/');
            } else {
                $filter['token' . $i] = null;
            }
            $i++;
        }

        $qa->match($filter);
        $qa->sort(array('weight' => 1));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param array  $nodeIds
     * @param string $siteId
     * @param string $language
     */
    public function removeByNodeIdsSiteIdAndLanguage(array $nodeIds, $siteId, $language)
    {
        $this->createQueryBuilder()
            ->remove()
            ->field('siteId')->equals($siteId)
            ->field('language')->equals($language)
            ->field('nodeId')->in($nodeIds)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $siteId
     */
    public function removeBySiteId($siteId)
    {
        $this->createQueryBuilder()
             ->remove()
             ->field('siteId')->equals($siteId)
             ->getQuery()
             ->execute();
    }
}

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
                $filter['token' . $i] = new \MongoRegex('/' . $workingPattern[$i] . '|\*/');
            } else {
                $filter['token' . $i] = null;
            }
            $i++;
        }

        $qa->match($filter);
        $qa->sort(array('weight' => 1));

        return $this->hydrateAggregateQuery($qa);
    }
}

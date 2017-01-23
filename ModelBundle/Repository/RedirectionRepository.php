<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Repository\RedirectionRepositoryInterface;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class RedirectionRepository
 */
class RedirectionRepository extends AbstractAggregateRepository implements RedirectionRepositoryInterface
{
    /**
     * @param string $nodeId
     * @param string $locale
     * @param string $siteId
     *
     * @return array
     */
    public function findByNode($nodeId, $locale, $siteId){
        $qa = $this->createAggregationQuery();

        $qa->match(array(
            'nodeId' => $nodeId,
            'locale' => $locale,
            'siteId' => $siteId,
        ));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $siteId
     *
     * @return array
     */
    public function findBySiteId($siteId){
        $qa = $this->createAggregationQuery();

        $qa->match(array(
            'siteId' => $siteId,
        ));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregationQuery();

        $this->filterSearch($configuration, $qa);

        $order = $configuration->getOrder();
        if (!empty($order)) {
            $qa->sort($order);
        }

        $qa->skip($configuration->getSkip());
        $qa->limit($configuration->getLimit());

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @return int
     */
    public function count()
    {
        $qa = $this->createAggregationQuery();

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return int
     */
    public function countWithFilter(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregationQuery();
        $this->filterSearch($configuration, $qa);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param Stage                       $qa
     *
     * @return array
     */
    protected function filterSearch(PaginateFinderConfiguration $configuration, Stage $qa)
    {
//         $qa->match(array('extra.site_id' => $configuration->getSearchIndex('site_id')));

        $siteName = $configuration->getSearchIndex('site_name');
        if (null !== $siteName && '' !== $siteName) {
            $qa->match(array('siteName' => new \MongoRegex('/.*' . $siteName . '.*/i')));
        }

        $locale = $configuration->getSearchIndex('locale');
        if (null !== $locale && '' !== $locale) {
            $qa->match(array('locale' => new \MongoRegex('/.*' . $locale . '.*/i')));
        }

        $routePattern = $configuration->getSearchIndex('route_pattern');
        if (null !== $routePattern && '' !== $routePattern) {
            $qa->match(array('routePattern' => new \MongoRegex('/.*' . $routePattern . '.*/i')));
        }

        $redirection = $configuration->getSearchIndex('redirection');
        if (null !== $redirection && '' !== $redirection) {
            $qa->match(array('$or' => array(
                array('url'    => new \MongoRegex('/.*' . $redirection . '.*/i')),
                array('nodeId' => new \MongoRegex('/.*' . $redirection . '.*/i'))
            )));
        }

        $permanent = $configuration->getSearchIndex('permanent');
        if (null !== $redirection && '' !== $permanent) {
            if ('true' == $permanent) {
                $qa->match(array('permanent' => true));
            }
            if ('false' == $permanent) {
                $qa->match(array('permanent' => false));
            }
        }

        return $qa;
    }
}

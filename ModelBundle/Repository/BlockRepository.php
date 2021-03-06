<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use OpenOrchestra\ModelInterface\Repository\BlockRepositoryInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class BlockRepository
 */
class BlockRepository extends AbstractAggregateRepository implements BlockRepositoryInterface
{
    /**
     * @param string $id
     *
     * @return null|\OpenOrchestra\ModelInterface\Model\ReadBlockInterface
     */
    public function findById($id) {
        return $this->find(new \MongoId($id));
    }

    /**
     * @param string $component
     * @param string $siteId
     * @param string $language
     *
     * @return array
     */
    public function findTransverseBlock($component, $siteId, $language)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language, true);

        $qa->match(array(
            'component' => $component
        ));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $code
     * @param string $language
     * @param string $siteId
     *
     * @return ReadBlockInterface|null
     */
    public function findOneTransverseBlockByCode($code, $language, $siteId)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array(
            'code' => $code,
            'language' => $language,
            'siteId' => $siteId,
            'transverse' => true
        ));

        return $this->singleHydrateAggregateQuery($qa);

    }

    /**
     * @param string                      $siteId
     * @param string                      $language
     * @param PaginateFinderConfiguration $configuration
     * @param boolean                     $transverse
     *
     * @return array
     */
    public function findForPaginateBySiteIdAndLanguage(
        PaginateFinderConfiguration $configuration,
        $siteId,
        $language,
        $transverse
    ) {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language, $transverse);
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
     * @param string                      $siteId
     * @param string                      $language
     * @param PaginateFinderConfiguration $configuration
     * @param boolean                     $transverse
     *
     * @return int
     */
    public function countWithFilterBySiteIdAndLanguage(
        PaginateFinderConfiguration $configuration,
        $siteId,
        $language,
        $transverse
    ) {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language, $transverse);
        $this->filterSearch($configuration, $qa);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string  $siteId
     * @param string  $language
     * @param boolean $transverse
     *
     * @return int
     */
    public function countBySiteIdAndLanguage($siteId, $language, $transverse)
    {
        $qa = $this->createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language, $transverse);

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
        $filter = array();
        $label = $configuration->getSearchIndex('label');
        if (null !== $label && $label !== '') {
            $filter['label'] = new \MongoRegex('/.*'.$label.'.*/i');
        }

        $components = $configuration->getSearchIndex('components');
        if (null !== $components && is_array($components) && !empty($components)) {
            $filter['component'] =  array('$in' => $components);
        }

        if (!empty($filter)) {
            $qa->match($filter);
        }

        return $qa;
    }

    /**
     * @param string  $siteId
     * @param string  $language
     * @param boolean $transverse
     *
     * @return Stage
     */
    protected function createAggregationQueryBuilderWithSiteIdAndLanguage($siteId, $language, $transverse)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array(
            'siteId' => $siteId,
            'language' => $language,
            'transverse' => $transverse
        ));

        return $qa;
    }
}

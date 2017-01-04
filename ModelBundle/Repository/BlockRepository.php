<?php

namespace OpenOrchestra\ModelBundle\Repository;

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
     * @return array
     */
    public function findTransverse(){
        $qa = $this->createAggregationQuery();

        $qa->match(array(
            'transverse' => true
        ));

        return $this->hydrateAggregateQuery($qa);
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
        $type = $configuration->getSearchIndex('type');

        if (null !== $type && '' !== $type) {
            $qa->match(array('type' => new \MongoRegex('/.*'.$type.'.*/i')));
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

<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\PaginateAndSearchFilterTrait;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

/**
 * Class ContentTypeRepository
 */
class ContentTypeRepository extends AbstractRepository implements ContentTypeRepositoryInterface
{
    use PaginateAndSearchFilterTrait;


    /**
     * @param $language
     *
     * @deprecated will be removed in 0.3.0, use findAllNotDeletedInLastVersion instead
     *
     * @return array
     */
    public function findAllByDeletedInLastVersion($language = null)
    {
        return $this->findAllNotDeletedInLastVersion($language);
    }

    /**
     * @param $language
     *
     * @return array
     */
    public function findAllNotDeletedInLastVersion($language = null)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'deleted' => false
            )
        );
        $elementName = 'contentType';
        $qa->group($this->generateLastVersionFilter($elementName));

        if ($language) {
            $qa->sort(
                array(
                    $elementName . '.names.' . $language. '.value' => 1
                )
            );
        }

        return $this->hydrateAggregateQuery($qa, $elementName, 'getContentTypeId');
    }

    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     * @param array|null  $order
     * @param int|null    $skip
     * @param int|null    $limit
     *
     * @deprecated will be removed in 0.3.0, use findAllNotDeletedInLastVersionForPaginate instead
     *
     * @return array
     */
    public function findAllByDeletedInLastVersionForPaginateAndSearch($descriptionEntity = null, $columns = null, $search = null, $order = null, $skip = null, $limit = null)
    {
        $configuration = PaginateFinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);
        $configuration->setPaginateConfiguration($order, $skip, $limit);

        return $this->findAllNotDeletedInLastVersionForPaginate($configuration);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    public function findAllNotDeletedInLastVersionForPaginate(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregateQueryNotDeletedInLastVersion();

        $qa = $this->generateFilter($qa, $configuration);

        $elementName = 'contentType';
        $qa->group($this->generateLastVersionFilter($elementName));

        $qa = $this->generateFilterSort(
            $qa,
            $configuration->getOrder(),
            $configuration->getDescriptionEntity(),
            $configuration->getColumns(), $elementName);

        $qa = $this->generateSkipFilter($qa, $configuration->getSkip());
        $qa = $this->generateLimitFilter($qa, $configuration->getLimit());

        return $this->hydrateAggregateQuery($qa, $elementName, 'getContentTypeId');
    }

    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     *
     * @deprecated will be removed in 0.3.0, use countNotDeletedInLastVersionWithSearchFilter instead
     *
     * @return int
     */
    public function countByDeletedInLastVersionWithSearchFilter($descriptionEntity = null, $columns = null, $search = null)
    {
        $configuration = FinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);

        return $this->countNotDeletedInLastVersionWithSearchFilter($configuration);
    }

    /**
     * @param FinderConfiguration $configuration
     *
     * @return int
     */
    public function countNotDeletedInLastVersionWithSearchFilter(FinderConfiguration $configuration)
    {
        $qa = $this->createAggregateQueryNotDeletedInLastVersion();
        $qa = $this->generateFilter($qa, $configuration);

        $elementName = 'contentType';
        $qa->group($this->generateLastVersionFilter($elementName));

        return $this->countDocumentAggregateQuery($qa, $elementName);
    }

    /**
     * @return int
     */
    public function countByContentTypeInLastVersion()
    {
        $qa = $this->createAggregateQueryNotDeletedInLastVersion();
        $elementName = 'content';
        $qa->group($this->generateLastVersionFilter($elementName));

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param string   $contentType
     *
     * @return ContentTypeInterface
     */
    public function findOneByContentTypeIdInLastVersion($contentType)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('contentTypeId' => $contentType));
        $qa->sort(array('version' => -1));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param $elementName
     *
     * @return array
     */
    protected function generateLastVersionFilter($elementName)
    {
        return array(
            '_id' => array('contentTypeId' => '$contentTypeId'),
            'version' => array('$max' => '$version'),
            $elementName => array('$last' => '$$ROOT')
        );
    }

    /**
     * @deprecated will be removed in 0.3.0, use createAggregateQueryNotDeletedInLastVersion instead
     *
     * @return \Solution\MongoAggregation\Pipeline\Stage
     */
    protected function createAggregateQueryByDeletedAndLastVersion()
    {
        return $this->createAggregateQueryNotDeletedInLastVersion();
    }

    /**
     * @return \Solution\MongoAggregation\Pipeline\Stage
     */
    protected function createAggregateQueryNotDeletedInLastVersion()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('deleted' => false));
        $qa->sort(array('contentTypeId' => -1));

        return $qa;
    }
}

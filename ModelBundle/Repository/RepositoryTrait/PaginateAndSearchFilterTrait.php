<?php

namespace OpenOrchestra\ModelBundle\Repository\RepositoryTrait;

use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Solution\MongoAggregation\Pipeline\Stage;

trait PaginateAndSearchFilterTrait
{
    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     * @param array|null  $order
     * @param int|null    $skip
     * @param int|null    $limit
     *
     * @deprecated will be removed in 0.3.0, use findForPaginate instead
     *
     * @return array
     */
    public function findForPaginateAndSearch($descriptionEntity = null, $columns = null, $search = null, $order = null, $skip = null, $limit = null)
    {
        $configuration = FinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);
        $paginateConfiguration = PaginateFinderConfiguration::generatePaginateFromVariable($configuration, $order, $skip, $limit);

        return $this->findForPaginate($paginateConfiguration);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregationQuery();
        $qa = $this->generateFilterForPaginate($qa,$configuration);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param array|null   $columns
     * @param array|null   $descriptionEntity
     * @param array|null   $search
     *
     * @deprecated will be removed in 0.3.0, use countWithFilter instead;
     *
     * @return int
     */
    public function countWithSearchFilter($descriptionEntity = null, $columns = null, $search = null)
    {
        $configuration = FinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);

        return $this->countWithFilter($configuration);
    }

    /**
     * @param FinderConfiguration $configuration
     *
     * @return int
     */
    public function countWithFilter(FinderConfiguration $configuration)
    {
        $qa = $this->createAggregationQuery();
        $qa = $this->generateFilter($qa, $configuration);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * Count all document
     *
     * @return int
     */
    public function count()
    {
        $qa = $this->createAggregationQuery();

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param Stage       $qa
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     *
     * @deprecated will be remove in 0.3.0, use generateFilter instead
     *
     * @return Stage
     */
    protected function generateFilterForSearch(Stage $qa, $descriptionEntity = null, $columns = null, $search = null)
    {
        $configuration = FinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);

        return $this->generateFilter($qa, $configuration);
    }

    /**
     * @param Stage               $qa
     * @param FinderConfiguration $configuration
     *
     * @return Stage
     */
    protected function generateFilter(Stage $qa, FinderConfiguration $configuration)
    {
        if (null !== $configuration->getColumns()) {
            $filterSearch = $this->generateSearchFilter($configuration);
            if (null !== $filterSearch) {
                $qa->match($filterSearch);
            }
        }

        return $qa;
    }

    /**
     * Create Query for paginate, order and filter by columns
     *
     * @param Stage       $qa
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     * @param array|null  $order
     * @param int|null    $skip
     * @param int|null    $limit
     *
     * @deprecated will be remove in 0.3.0, use generateFilterForPaginate instead
     *
     * @return Stage
     */
    protected function generateFilterForPaginateAndSearch(Stage $qa, $descriptionEntity = null, $columns = null, $search = null, $order = null, $skip = null, $limit = null)
    {
        $configuration = FinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);
        $paginateConfiguration = PaginateFinderConfiguration::generatePaginateFromVariable($configuration, $order, $skip, $limit);

        return $this->generateFilterForPaginate($qa, $paginateConfiguration);
    }

    /**
     * @param Stage                       $qa
     * @param PaginateFinderConfiguration $configuration
     *
     * @return Stage
     */
    protected function generateFilterForPaginate(Stage $qa, PaginateFinderConfiguration $configuration)
    {
        $qa = $this->generateFilter($qa, $configuration);
        $qa = $this->generateFilterSort($qa, $configuration->getOrder(), $configuration->getDescriptionEntity(), $configuration->getColumns());
        $qa = $this->generateSkipFilter($qa, $configuration->getSkip());
        $qa = $this->generateLimitFilter($qa, $configuration->getLimit());

        return $qa;
    }

    /**
     * @param Stage        $qa
     * @param integer|null $limit
     *
     * @return Stage
     */
    protected function generateLimitFilter(Stage $qa, $limit = null)
    {
        if (null !== $limit) {
            $qa->limit($limit);
        }

        return $qa;
    }

    /**
     * @param Stage        $qa
     * @param integer|null $skip
     *
     * @return Stage
     */
    protected function generateSkipFilter(Stage $qa, $skip = null)
    {
        if (null !== $skip && $skip > 0) {
            $qa->skip($skip);
        }

        return $qa;
    }

    /**
     * @param Stage       $qa
     * @param array|null  $order
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $elementName
     *
     * @return Stage
     */
    protected function generateFilterSort(Stage $qa, $order = null , $descriptionEntity = null, $columns = null, $elementName = null)
    {
        if (null !== $order && null !== $columns) {
            $filterOrder = $this->generateOrderFilter($order, $descriptionEntity, $columns, $elementName);
            if (null !== $filterOrder) {
                $qa->sort($filterOrder);
            }
        }

        return $qa;
    }

    /**
     * @param array  $order
     * @param array  $descriptionEntity
     * @param array  $columns
     * @param string $elementName
     *
     * @return array|null
     */
    protected function generateOrderFilter($order, $descriptionEntity, $columns, $elementName)
    {
        $filter = array();

        foreach ($order as $orderColumn) {
            $numberColumns = $orderColumn['column'];
            if ($columns[$numberColumns]['orderable']) {
                if (!empty($columns[$numberColumns]['name'])) {
                    $columnsName = $columns[$numberColumns]['name'];
                    if (isset($descriptionEntity[$columnsName]) && isset($descriptionEntity[$columnsName]['key'])) {
                        $name = (null === $elementName)? $descriptionEntity[$columnsName]['key'] :  $elementName.'.'.$descriptionEntity[$columnsName]['key'];
                        $dir = ($orderColumn['dir'] == 'desc') ? -1 : 1;
                        $filter[$name] = $dir;
                    }
                }
            }
        }

        return (!empty($filter)) ? $filter : null;
    }

    /**
     * Generate filter for search text in field
     *
     * @param string $name
     * @param string $value
     * @param string $type
     *
     * @return array
     */
    protected function generateFilterSearchField($name, $value, $type)
    {
        if ($type == 'integer') {
            $filter = array($name => (int) $value);
        } elseif ($type == 'boolean') {
            $value = ($value === 'true' || $value === '1') ? true : false;
            $filter = array($name => $value);
        } else {
            $value = preg_quote($value);
            $filter = array($name => new \MongoRegex('/.*'.$value.'.*/i'));
        }

        return $filter;
    }

    /**
     * Generate filter for search text in one or more fields
     *
     * @param array  $columns
     * @param array  $descriptionEntity
     * @param string $search global search
     *
     * @deprecated will be removed in 0.3.0, use generateSearchFilter instead
     *
     * @return array|null
     */
    protected function generateFilterSearch($descriptionEntity, $columns, $search)
    {
        $configuration = FinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);

        return $this->generateSearchFilter($configuration);
    }

    /**
     * @param FinderConfiguration $configuration
     *
     * @return array|null
     */
    protected function generateSearchFilter(FinderConfiguration $configuration)
    {
        $filter = null;

        $filtersAll = array();
        $filtersColumn = array();
        foreach ($configuration->getColumns() as $column) {
            $columnsName = $column['name'];
            $descriptionEntity = $configuration->getDescriptionEntity();
            if (isset($descriptionEntity[$columnsName]) && isset($descriptionEntity[$columnsName]['key'])) {
                $descriptionAttribute = $descriptionEntity[$columnsName];
                $name = $descriptionAttribute['key'];
                $type = isset($descriptionAttribute['type']) ? $descriptionAttribute['type'] : null;
                if ($column['searchable'] && !empty($column['search']['value']) && !empty($name)) {
                    $value = $column['search']['value'];
                    $filtersColumn[] = $this->generateFilterSearchField($name, $value, $type);
                }
                $search = $configuration->getSearch();
                if (!empty($search) && $column['searchable'] && !empty($name)) {
                    $filtersAll[] = $this->generateFilterSearchField($name, $search, $type);
                }
            }
        }

        if (!empty($filtersAll) || !empty($filtersColumn)) {
            $filter = array('$and' => $filtersColumn);
            if (!empty($filtersAll) && empty($filtersColumn)) {
                $filter = array('$or' => $filtersAll);
            } elseif (!empty($filtersAll) && !empty($filtersColumn)) {
                $filter = array('$and'=>array(
                    array('$and' => $filtersColumn),
                    array('$or' => $filtersAll),
                ));
            }
        }

        return $filter;
    }
}

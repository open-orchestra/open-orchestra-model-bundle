<?php

namespace OpenOrchestra\ModelBundle\Repository\RepositoryTrait;

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
     * @return array
     */
    public function findForPaginateAndSearch($descriptionEntity = null, $columns = null, $search = null, $order = null, $skip = null, $limit = null)
    {
        $qa = $this->createAggregationQuery();
        $qa = $this->generateFilterForPaginateAndSearch($qa, $descriptionEntity, $columns, $search, $order, $skip, $limit);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param array|null   $columns
     * @param array|null   $descriptionEntity
     * @param array|null   $search
     *
     * @return int
     */
    public function countWithSearchFilter($descriptionEntity = null, $columns = null, $search = null)
    {
        $qa = $this->createAggregationQuery();
        $qa = $this->generateFilterForSearch($qa, $descriptionEntity, $columns, $search);

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
     * @param Stage $qa
     *
     * @return int
     */
    protected function countDocumentAggregateQuery(Stage $qa)
    {
        $qa->group(array(
            '_id' => null,
            'count' => array('$sum' => 1)
        ));
        $res = $qa->getQuery()->aggregate();

        return (null !== $res[0]['count']) ? $res[0]['count'] : 0;
    }

    /**
     * @param Stage       $qa
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     *
     * @return Stage
     */
    protected function generateFilterForSearch(Stage $qa, $descriptionEntity = null, $columns = null, $search = null)
    {
        if (null !== $columns) {
            $filterSearch = $this->generateFilterSearch($descriptionEntity, $columns, $search);
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
     * @return Stage
     */
    protected function generateFilterForPaginateAndSearch(Stage $qa, $descriptionEntity = null, $columns = null, $search = null, $order = null, $skip = null, $limit = null)
    {
        $qa = $this->generateFilterForSearch($qa, $descriptionEntity, $columns, $search);
        $qa = $this->generateFilterSort($qa, $order, $descriptionEntity, $columns);
        $qa = $this->generateSkipFilter($qa, $skip);
        $qa = $this->generateLimitFilter($qa, $limit);

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
     * @return array|null
     */
    protected function generateFilterSearch($descriptionEntity, $columns, $search)
    {
        $filter = null;

        $filtersAll = array();
        $filtersColumn = array();
        foreach ($columns as $column) {
            $columnsName = $column['name'];
            if (isset($descriptionEntity[$columnsName]) && isset($descriptionEntity[$columnsName]['key'])) {
                $descriptionAttribute = $descriptionEntity[$columnsName];
                $name = $descriptionAttribute['key'];
                $type = isset($descriptionAttribute['type']) ? $descriptionAttribute['type'] : null;
                if ($column['searchable'] && !empty($column['search']['value']) && !empty($name)) {

                    $value = $column['search']['value'];
                    $filtersColumn[] = $this->generateFilterSearchField($name, $value, $type);
                }
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

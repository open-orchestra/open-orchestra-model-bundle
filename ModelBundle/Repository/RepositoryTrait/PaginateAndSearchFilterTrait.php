<?php

namespace OpenOrchestra\ModelBundle\Repository\RepositoryTrait;

use Solution\MongoAggregation\Pipeline\Stage;

trait PaginateAndSearchFilterTrait
{
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

        return $res[0]['count'];
    }

    /**
     * @param Stage       $qa
     * @param array|null  $columns
     * @param string|null $search
     *
     * @return Stage
     */
    protected function generateFilterForSearch($qa, $columns = null, $search = null)
    {
        if (null !== $columns) {
            $filterSearch = $this->generateFilterSearch($columns, $search);
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
     * @param array|null  $columns
     * @param string|null $search
     * @param array|null  $order
     * @param int|null    $skip
     * @param int|null    $limit
     *
     * @return Stage
     */
    protected function generateFilterForPaginateAndSearch($qa, $columns = null, $search = null, $order = null, $skip = null, $limit = null)
    {
        $qa = $this->generateFilterForSearch($qa, $columns, $search);

        if (null !== $order && null !== $columns) {
            $filterOrder = $this->generateOrderFilter($order, $columns);
            if (null !== $filterOrder) {
                $qa->sort($filterOrder);
            }
        }

        if (null !== $skip && $skip > 0) {
            $qa->skip($skip);
        }

        if (null !== $limit) {
            $qa->limit($limit);
        }

        return $qa;
    }

    /**
     * @param array $order
     * @param array $columns
     *
     * @return array|null
     */
    protected function generateOrderFilter($order, $columns)
    {
        $filter = array();

        foreach ($order as $orderColumn) {
            $numberColumns = $orderColumn['column'];
            if ($columns[$numberColumns]['orderable']) {
                $name = $columns[$numberColumns]['name'];
                $dir = ($orderColumn['dir'] == 'desc') ? -1 : 1;
                $filter[$name] = $dir;
            }
        }

        return (!empty($filter)) ? $filter : null;
    }

    /**
     * Generate filter for search text in field
     *
     * @param string $name
     * @param string $value
     *
     * @return array
     */
    protected function generateFilterSearchField($name, $value)
    {
        return array($name => new \MongoRegex('/.*'.$value.'.*/i'));
    }

    /**
     * Generate filter for search text in one or more fields
     *
     * @param array  $columns
     * @param string $search global search
     *
     * @return array|null
     */
    protected function generateFilterSearch($columns, $search)
    {
        $filter = null;

        $filtersAll = array();
        $filtersColumn = array();

        foreach ($columns as $column) {
            $name = $column['name'];
            if ($column['searchable'] && !empty($column['search']['value']) && !empty($name)) {
                $value = $column['search']['value'];
                $filtersColumn[] = $this->generateFilterSearchField($name, $value);
            }
            if (!empty($search) && $column['searchable'] && !empty($name)) {
                $filtersAll[] = $this->generateFilterSearchField($name, $search);
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

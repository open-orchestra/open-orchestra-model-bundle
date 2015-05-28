<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Solution\MongoAggregation\Pipeline\Stage;
use Solution\MongoAggregationBundle\AggregateQuery\AggregationQueryBuilder;

/**
 * Class AbstractRepository
 */
abstract class AbstractRepository extends DocumentRepository
{
    /**
     * @var AggregationQueryBuilder
     */
    private $aggregationQueryBuilder;

    /**
     * @param AggregationQueryBuilder $aggregationQueryBuilder
     */
    public function setAggregationQueryBuilder($aggregationQueryBuilder)
    {
        $this->aggregationQueryBuilder = $aggregationQueryBuilder;
    }

    /**
     * @param string|null $stage
     *
     * @return Stage
     */
    protected function createAggregationQuery($stage = null)
    {
        return $this->aggregationQueryBuilder->getCollection($this->getClassName())->createAggregateQuery($stage);
    }

    /**
     * @param Stage  $qa
     * @param string $elementName
     * @param string $idSelector
     *
     * @return array
     */
    protected function hydrateAggregateQuery(Stage $qa, $elementName = null, $idSelector = null)
    {
        $contents = $qa->getQuery()->aggregate();
        $contentCollection = array();

        foreach ($contents as $content) {
            if (null !== $elementName) {
                $content = $content[$elementName];
            }

            $content = $this->getDocumentManager()->getUnitOfWork()->getOrCreateDocument($this->getClassName(), $content);
            if ($idSelector) {
                $contentCollection[$content->$idSelector()] = $content;
            } else {
                $contentCollection[] = $content;
            }
        }

        return $contentCollection;
    }

    /**
     * Create Query for paginate, order and filter by columns
     *
     * @param integer|null $limit
     * @param integer|null $skip
     * @param array|null   $columns
     * @param array|null   $order
     * @param string|null  $search
     *
     * @return Stage
     */
    protected function createAggregationQueryForPaginateAndSearch($limit = null, $skip = null, $columns = null, $order = null, $search = null)
    {
        $qa = $this->createAggregationQuery();

        if( null !== $columns) {
            $filterSearch = $this->generateFilterSearch($columns, $search);
            if (null !== $filterSearch) {
                $qa->match($filterSearch);
            }
        }

        if (null !== $skip) {
            $qa->skip($skip);
        }

        if (null !== $limit) {
            $qa->limit($limit);
        }

        if( null !== $order && null !== $columns) {
            $filterOrder = $this->generateOrderFilter($order, $columns);
            if (null !== $filterOrder) {
                $qa->sort($filterOrder);
            }
        }

        return $qa;
    }

    /**
     * @param array $order
     * @param array $columns
     *
     * @return array|null
     */
    protected function generateOrderFilter($order, $columns){
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
    protected function generateFilterSearchField($name, $value){
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

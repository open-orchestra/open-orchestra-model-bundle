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
}

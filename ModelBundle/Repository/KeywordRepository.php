<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\UseTrackableTrait;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class KeywordRepository
 */
class KeywordRepository extends AbstractAggregateRepository implements KeywordRepositoryInterface
{
    use UseTrackableTrait;

    /**
     * @param string $label
     *
     * @return KeywordInterface|null
     */
    public function findOneByLabel($label)
    {
        return $this->findOneBy(array('label' => $label));
    }

    /**
     * @return mixed
     */
    public function getManager()
    {
        return $this->getDocumentManager();
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
     * @return int
     */
    public function count()
    {
        $qa = $this->createAggregationQuery();

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param array $keywordIds
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function removeKeywords(array $keywordIds)
    {
        array_walk($keywordIds, function(&$keywordId) {$keywordId = new \MongoId($keywordId);});

        $qb = $this->createQueryBuilder();
        $qb->remove()
            ->field('id')->in($keywordIds)
            ->getQuery()
            ->execute();
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param Stage                       $qa
     *
     * @return array
     */
    protected function filterSearch(PaginateFinderConfiguration $configuration, Stage $qa)
    {
        $label = $configuration->getSearchIndex('label');

        if (null !== $label && '' !== $label) {
            $qa->match(array('label' => new \MongoRegex('/.*'.$label.'.*/i')));
        }

        return $qa;
    }
}

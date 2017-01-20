<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class StatusRepository
 */
class StatusRepository extends AbstractAggregateRepository implements StatusRepositoryInterface
{
    /**
     * @param string $id
     *
     * @return StatusInterface
     */
    public function findOneById($id)
    {
        return parent::findOneById($id);
    }

    /**
     * @parameter array $order
     *
     * @return array
     */
    public function findNotOutOfWorkflow(array $order = array('name' => 1))
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('outOfWorkflow' => false));
        $qa->sort($order);

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @return StatusInterface
     */
    public function findOneByInitial()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('initialState' => true));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function findOtherByInitial($name)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'name'    => array('$ne' => $name),
                'initialState' => true,
            )
        );

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @return StatusInterface
     */
    public function findOneByPublished()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('publishedState' => true));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @return array
     */
    public function findByAutoPublishFrom()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('autoPublishFromState' => true));

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @return StatusInterface
     */
    public function findOnebyAutoUnpublishTo()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('autoUnpublishToState' => true));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function findOtherByAutoUnpublishTo($name)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'name' => array('$ne' => $name),
                'autoUnpublishToState' => true
            )
        );

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function findOtherByTranslationState($name)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(
            array(
                'name' => array('$ne' => $name),
                'translationState' => true
            )
        );

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @return StatusInterface
     */
    public function findOneByTranslationState()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('translationState' => true));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @return StatusInterface
     */
    public function findOneByEditable()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('publishedState' => false));

        return $this->singleHydrateAggregateQuery($qa);
    }

    /**
     * @return StatusInterface
     */
    public function findOneByOutOfWorkflow()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('outOfWorkflow' => true));

        return $this->singleHydrateAggregateQuery($qa);
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
        $label = $configuration->getSearchIndex('label');
        $language = $configuration->getSearchIndex('language');

        if (null !== $label && '' !== $label && null !== $language && '' !== $language) {
            $qa->match(array('labels.' . $language => new \MongoRegex('/.*'.$label.'.*/i')));
        }

        return $qa;
    }

    /**
     * @param array $statusIds
     *
     * @throws \Exception
     */
    public function removeStatuses(array $statusIds)
    {
        $statusMongoIds = array();
        foreach ($statusIds as $statusId) {
            $statusMongoIds[] = new \MongoId($statusId);
        }

        $qb = $this->createQueryBuilder();
        $qb->remove()
            ->field('id')->in($statusMongoIds)
            ->getQuery()
            ->execute();
    }
}

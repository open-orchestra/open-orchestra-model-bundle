<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;

/**
 * Class StatusRepository
 */
class StatusRepository extends AbstractAggregateRepository implements StatusRepositoryInterface
{
    use PaginationTrait;

    /**
     * @return StatusInterface
     */
    public function findOneByInitial()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('initial' => true));

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
                'initial' => true,
            )
        );

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @return StatusInterface
     */
    public function findOneByEditable()
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('published' => false));

        return $this->singleHydrateAggregateQuery($qa);
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\PaginateAndSearchFilterTrait;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;

/**
 * Class StatusRepository
 */
class StatusRepository extends AbstractRepository implements StatusRepositoryInterface
{
    use PaginateAndSearchFilterTrait;

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
        $qa->match(array('name' => array('$ne' => $name)));
        $qa->match(array('initial' => true));

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

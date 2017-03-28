<?php

namespace OpenOrchestra\ModelBundle\Repository\RepositoryTrait;

use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Trait StatusableTrait
 */
trait StatusableTrait
{
    /**
     * @param StatusInterface $status
     */
    public function updateEmbeddedStatus(StatusInterface $status)
    {
        $this->createQueryBuilder()
            ->updateMany()
            ->field('status.id')->equals($status->getId())
            ->field('status')->set($status)
            ->getQuery()
            ->execute();
    }

    /**
     * @param StatusInterface $status
     *
     * @return bool
     */
    public function hasStatusedElement(StatusInterface $status)
    {
        $qa = $this->createAggregationQuery();
        $qa->match(array('status._id' => new \MongoId($status->getId())));

        return 0 !== $this->countDocumentAggregateQuery($qa);
    }
}

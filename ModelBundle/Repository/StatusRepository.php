<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;

/**
 * Class StatusRepository
 */
class StatusRepository extends AbstractRepository implements StatusRepositoryInterface
{
    /**
     * @return StatusInterface
     */
    public function findOneByInitial()
    {
        $qb = $this->createQueryBuilder();
        $qb->field('initial')->equals(true);

        return $qb->getQuery()->execute()->getSingleResult();
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function findOtherByInitial($name)
    {
        $qb = $this->createQueryBuilder();
        $qb->field('name')->notEqual($name);
        $qb->field('initial')->equals(true);

        return $qb->getQuery()->execute();
    }

    /**
     * @return StatusInterface
     */
    public function findOneByEditable()
    {
        $qb = $this->createQueryBuilder();
        $qb->field('published')->equals(false);

        return $qb->getQuery()->execute()->getSingleResult();
    }
}

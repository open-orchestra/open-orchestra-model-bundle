<?php

namespace OpenOrchestra\ModelBundle\Repository;

use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use OpenOrchestra\Pagination\MongoTrait\PaginationTrait;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\ModelBundle\Repository\RepositoryTrait\UseTrackableTrait;

/**
 * Class KeywordRepository
 */
class KeywordRepository extends AbstractAggregateRepository implements KeywordRepositoryInterface
{
    use PaginationTrait;
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
}

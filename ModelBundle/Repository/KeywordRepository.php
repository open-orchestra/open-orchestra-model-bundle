<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;

/**
 * Class KeywordRepository
 */
class KeywordRepository extends DocumentRepository implements KeywordRepositoryInterface
{
    /**
     * @param string $label
     *
     * @return KeywordInterface
     */
    public function findOneByLabel($label)
    {
        return $this->findOneBy(array('label' => $label));
    }
}

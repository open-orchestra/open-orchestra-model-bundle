<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class Status
 *
 * @ODM\Document(
 *   collection="status",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\StatusRepository"
 * )
 */
class Status extends AbstractStatus
{
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}

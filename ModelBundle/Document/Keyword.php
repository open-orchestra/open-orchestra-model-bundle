<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class Keyword
 *
 * @ODM\Document(
 *   collection="keyword",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\KeywordRepository"
 * )
 */
class Keyword extends AbstractKeyword
{
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getLabel();
    }
}

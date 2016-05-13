<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\EmbedKeywordInterface;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;

@trigger_error('The '.__NAMESPACE__.'\EmbedKeyword class is deprecated since version 1.2.0 and will be removed in 1.2.1', E_USER_DEPRECATED);

/**
 * Class EmbedKeyword
 *
 * @ODM\EmbeddedDocument
 */
class EmbedKeyword extends AbstractKeyword implements EmbedKeywordInterface
{
    /**
     * @param KeywordInterface $keyword
     */
    public function __construct(KeywordInterface $keyword)
    {
        $this->id = $keyword->getId();
        $this->setLabel($keyword->getLabel());
    }

    /**
     * @param KeywordInterface $keyword
     *
     * @return EmbedKeywordInterface
     */
    public static function createFromKeyword(KeywordInterface $keyword)
    {
        return new EmbedKeyword($keyword);
    }
}

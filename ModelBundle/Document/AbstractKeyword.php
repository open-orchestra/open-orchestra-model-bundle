<?php

namespace OpenOrchestra\ModelBundle\Document;

use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use OpenOrchestra\MongoTrait\UseTrackable;

/**
 * Class AbstractKeyword
 */
abstract class AbstractKeyword implements KeywordInterface
{
    use UseTrackable;

    /**
     * @ODM\Id()
     */
    protected $id;

    /**
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="label")
     */
    protected $label;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
}

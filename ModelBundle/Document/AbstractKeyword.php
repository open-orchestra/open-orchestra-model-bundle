<?php

namespace OpenOrchestra\ModelBundle\Document;

use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AbstractKeyword
 */
abstract class AbstractKeyword implements KeywordInterface
{
    /**
     * @ODM\Id()
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @ODM\Field(type="string")
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

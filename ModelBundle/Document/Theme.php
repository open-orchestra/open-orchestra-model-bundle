<?php

namespace PHPOrchestra\ModelBundle\Document;

use PHPOrchestra\ModelInterface\Model\ThemeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class Theme
 *
 * @ODM\Document(
 *   collection="theme",
 *   repositoryClass="PHPOrchestra\ModelBundle\Repository\ThemeRepository"
 * )
 */
class Theme implements ThemeInterface
{
    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}

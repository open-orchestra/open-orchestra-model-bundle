<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use DateTime;

/**
 * Class TrashCan
 *
 * @ODM\Document(
 *   collection="trashcan",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\TrashItemRepository"
 * )
 */
class TrashItem implements TrashItemInterface
{
    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $name

     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="name")
     */
    protected $name;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="delete_at")
     */
    protected $deleteAt;

    /**
     * @ODM\ReferenceOne
     */
    private $entity;

    /**
     * Build new instance
     */
    public function __construct()
    {
        $date = new DateTime();
        $this->deleteAt =  $date->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDeleteAt()
    {
        return $this->deleteAt;
    }

    /**
     * @param string $deleteAt
     */
    public function setDeleteAt($deleteAt)
    {
        $this->deleteAt = $deleteAt;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
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

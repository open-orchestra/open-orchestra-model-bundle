<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use DateTime;

/**
 * Class TrashItem
 *
 * @ODM\Document(
 *   collection="trash_item",
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
     */
    protected $name;

    /**
     * @var string $type

     * @ODM\Field(type="string")
     */
    protected $type;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $deletedAt;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $siteId;

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
        $this->deletedAt =  $date->format('Y-m-d H:i:s');
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
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param string $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
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

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param string $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }
}

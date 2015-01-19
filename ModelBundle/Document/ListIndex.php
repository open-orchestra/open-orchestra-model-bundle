<?php

namespace PHPOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use PHPOrchestra\ModelInterface\Model\ListIndexInterface;

/**
 * Class ListIndex
 *
 * @ODM\Document(
 *   collection="list_index",
 *   repositoryClass="PHPOrchestra\ModelBundle\Repository\ListIndexRepository"
 * )
 */
class ListIndex implements ListIndexInterface
{
    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $nodeId
     *
     * @ODM\Field(type="string")
     */
    protected $nodeId;

    /**
     * @var string $contentId
     *
     * @ODM\Field(type="string")
     */
    protected $contentId;

    /**
     * @param string $nodeId
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * @return string
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @param string $contentId
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;
    }

    /**
     * @return string
     */
    public function getContentId()
    {
        return $this->contentId;
    }
}

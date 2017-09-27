<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\InternalUrlInterface;

/**
 * Description of InternalUrl
 *
 * @ODM\EmbeddedDocument
 */
class InternalUrl implements InternalUrlInterface
{
    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $siteId;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $aliasId;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $nodeId;

    /**
     * @var string
     *
     * @ODM\Field(type="hash")
     */
    protected $wildcards;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $query;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->wildcard = array();
    }

   /**
     * @return string
     */
    public function getSiteId()
    {
        return (string) $this->siteId;
    }

    /**
     * @param string $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * @return string
     */
    public function getAliasId()
    {
        return $this->aliasId;
    }

    /**
     * @param string $aliasId
     */
    public function setAliasId($aliasId)
    {
        $this->aliasId = $aliasId;
    }

    /**
     * @return string
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @param string $nodeId
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * @return array
     */
    public function getWildcards()
    {
        return $this->wildcards;
    }

    /**
     * Set wildcards
     *
     * @param array $wildcards
     */
    public function setWildcards(array $wildcards)
    {
        $this->wildcards = $wildcards;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }
}

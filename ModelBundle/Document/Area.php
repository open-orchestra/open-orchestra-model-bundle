<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Description of Area
 *
 * @ODM\EmbeddedDocument
 */
class Area implements AreaInterface
{
    /**
     * @ODM\ReferenceMany(targetDocument="OpenOrchestra\ModelInterface\Model\BlockInterface")
     */
    protected $blocks = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initializeCollections();
    }

    /**
     * Set blocks
     *
     * @param array $blocks
     */
    public function setBlocks(ArrayCollection $blocks)
    {
        $this->blocks->clear();
        foreach ($blocks as $block) {
            $this->blocks->add($block);
        }
    }

    /**
     * @param int            $key
     * @param BlockInterface $block
     */
    public function setBlock($key, BlockInterface $block)
    {
        $this->blocks->set($key, $block);
    }

    /**
     * @param array $block
     */
    public function addBlock(BlockInterface $block)
    {
        $this->blocks->add($block);
    }

    /**
     * Remove block
     *
     * @param BlockInterface $block
     */
    public function removeBlock(BlockInterface $block)
    {
        $this->blocks->removeElement($block);
    }

    /**
     * Remove block with index $key
     *
     * @param string $key
     */
    public function removeBlockWithKey($key)
    {
        $this->blocks->remove($key);
    }

    /**
     * Get blocks
     *
     * @return array $blocks
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool|int|mixed|string
     */
    public function getBlockIndex(BlockInterface $block)
    {
        return $this->blocks->indexOf($block);
    }

    /**
     * Initialize collections
     */
    protected function initializeCollections()
    {
        $this->blocks = new ArrayCollection();
    }

    /**
     * Initialize collections on clone
     */
    public function __clone()
    {
        $this->initializeCollections();
    }
}

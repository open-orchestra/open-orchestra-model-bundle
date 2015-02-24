<?php

namespace OpenOrchestra\ModelBundle\Test\Document;

use OpenOrchestra\ModelBundle\Document\Site;

/**
 * Class SiteTest
 */
class SiteTest extends \PHPUnit_Framework_TestCase
{
    protected $site;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->site = new Site();
    }

    /**
     * Test removeBlock
     */
    public function testRemoveBlockWithEmptyBlocks()
    {
        $this->site->removeBlock('block');

        $this->assertSame(array(), $this->site->getBlocks());
    }

    /**
     * Test removeBlock
     *
     * @param string $block
     * @param array  $expected
     *
     * @dataProvider provideBlocks
     */
    public function testRemoveBlock($block, $expected)
    {
        $this->site->addBlock('block1');
        $this->site->addBlock('block2');
        $this->site->addBlock('block3');

        $this->site->removeBlock($block);

        $this->assertSame($expected, $this->site->getBlocks());
    }

    /**
     * @return array
     */
    public function provideBlocks()
    {
        return array(
            array('block', array('block1', 'block2', 'block3')),
            array('block1', array('block2', 'block3')),
            array('block2', array('block1', 'block3')),
            array('block3', array('block1', 'block2')),
        );
    }

}

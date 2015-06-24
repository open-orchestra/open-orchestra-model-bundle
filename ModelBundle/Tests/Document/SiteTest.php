<?php

namespace OpenOrchestra\ModelBundle\Tests\Document;

use OpenOrchestra\ModelBundle\Document\Site;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Phake;

/**
 * Class SiteTest
 */
class SiteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SiteInterface
     */
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

    /**
     * @param int    $aliasId
     * @param string $language
     *
     * @dataProvider provideAliasIdAndLanguage
     */
    public function testGetAliasIdForLanguage($aliasId, $language)
    {
        $alias1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($alias1)->getLanguage()->thenReturn('fr');
        $alias2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($alias2)->getLanguage()->thenReturn('en');

        $this->site->addAlias($alias1);
        $this->site->addAlias($alias2);

        $this->assertSame($aliasId, $this->site->getAliasIdForLanguage($language));
    }

    /**
     * @return array
     */
    public function provideAliasIdAndLanguage()
    {
        return array(
            array(0, 'fr'),
            array(1, 'en'),
        );
    }
}

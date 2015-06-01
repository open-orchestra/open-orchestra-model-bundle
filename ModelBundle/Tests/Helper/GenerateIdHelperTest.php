<?php

namespace OpenOrchestra\ModelBundle\Tests\Helper;

use OpenOrchestra\ModelBundle\Helper\GenerateIdHelper;
use Phake;

/**
 * Test GenerateIdHelperTest
 */
class GenerateIdHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenerateIdHelper
     */
    protected $helper;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->helper = new GenerateIdHelper();
    }

    /**
     * @param string $input
     * @param string $output
     *
     * @dataProvider provideInputAndOutput
     */
    public function testGenerate($input, $output)
    {
        $this->assertSame($output, $this->helper->generate($input));
    }

    /**
     * @return array
     */
    public function provideInputAndOutput()
    {
        return array(
            array('foo', 'foo'),
            array('fooBar', 'foo_bar'),
            array('foo bar', 'foo_bar'),
            array('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ', 'aaaaaceeeeiiiinooooouuuuyyaaaaaceeeeiiiinooooouuuuy'),
            array('f%oo/\\b?a!r', 'foobar'),
            array('foo   ', 'foo'),
            array('   foo', 'foo'),
            array('f\'oo', 'foo'),
            array('f"oo', 'foo'),
        );
    }
}

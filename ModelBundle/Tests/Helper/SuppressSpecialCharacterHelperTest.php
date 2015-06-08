<?php

namespace OpenOrchestra\ModelBundle\Tests\Helper;

use OpenOrchestra\ModelBundle\Helper\SuppressSpecialCharacterHelper;
use Phake;

/**
 * Class SuppressSpecialCharacterHelperTest
 */
class SuppressSpecialCharacterHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SuppressSpecialCharacterHelper
     */
    protected $helper;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->helper = new SuppressSpecialCharacterHelper();
    }

    /**
     * @param string $input
     * @param string $output
     * @param array  $authorizeCharacter
     *
     * @dataProvider provideInputAndOutput
     */
    public function testTransform($input, $output, $authorizeCharacter = array())
    {
        $this->assertSame($output, $this->helper->transform($input, $authorizeCharacter));
    }

    /**
     * @return array
     */
    public function provideInputAndOutput()
    {
        return array(
            array('foo', 'foo'),
            array('foo bar', 'foo_bar'),
            array('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ', 'aaaaaceeeeiiiinooooouuuuyyaaaaaceeeeiiiinooooouuuuy'),
            array('f%oo/\\b?a!r', 'foobar'),
            array('foo   ', 'foo'),
            array('   foo', 'foo'),
            array('f\'oo', 'foo'),
            array('f"oo', 'foo'),
            array('test_test', 'test_test', array('_')),
            array('test.test_a', 'test.test_a', array('_', '.')),
            array('foo bar.téçst', 'foo_bar.tecst', array('_', '.')),
        );
    }
}
<?php

namespace OpenOrchestra\ModelBundle\Tests\Document;

use OpenOrchestra\ModelBundle\Document\RouteDocument;
use Phake;

/**
 * Test RouteDocumentTest
 */
class RouteDocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RouteDocument
     */
    protected $document;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->document = new RouteDocument();
    }

    /**
     * @param string $pattern
     * @param array  $tokens
     * @param int    $weight
     *
     * @dataProvider providePatternAndDecomposition
     */
    public function testSetPattern($pattern, array $tokens, $weight)
    {
        $this->document->setPattern($pattern);

        foreach ($tokens as $key => $token) {
            $tokenGetter = 'getToken' . $key;
            $this->assertSame($token, $this->document->$tokenGetter());
        }

        $this->assertSame($pattern, $this->document->getPattern());
        $this->assertEquals($weight, $this->document->getWeight());
    }

    /**
     * @return array
     */
    public function providePatternAndDecomposition()
    {
        return array(
            array('', array(), 0),
            array('foo', array('foo'), 0),
            array('/foo', array('foo'), 0),
            array('foo/bar', array('foo', 'bar'), 0),
            array('/foo/bar/', array('foo', 'bar'), 0),
            array('{foo}/bar', array('*', 'bar'), 1),
            array('foo/{bar}', array('foo', '*'), 10),
            array('foo/{bar}/baz', array('foo', '*', 'baz'), 10),
            array('{foo}/{bar}/{baz}', array('*', '*', '*'), 111),
            array('zero/one/two/three/four/five/six/seven/eight/nine/ten', array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten'), 0),
            array('zero/one/two/three/four/five/six/{seven}/eight/nine/ten', array('zero', 'one', 'two', 'three', 'four', 'five', 'six', '*', 'eight', 'nine', 'ten'), 10000000),
        );
    }
}

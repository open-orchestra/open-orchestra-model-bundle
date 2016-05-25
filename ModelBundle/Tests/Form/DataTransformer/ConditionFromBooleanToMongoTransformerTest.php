<?php

namespace OpenOrchestra\ModelBundle\Tests\Form\DataTransformer;

use OpenOrchestra\ModelBundle\Form\DataTransformer\ConditionFromBooleanToMongoTransformer;
use Phake;

/**
 * Class ConditionFromBooleanToMongoTransformerTest
 */
class ConditionFromBooleanToMongoTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConditionFromBooleanToMongoTransformer
     */
    protected $transformer;

    public function setUp()
    {
        $this->transformer = new ConditionFromBooleanToMongoTransformer();

        $this->transformer->setField('keywords');
    }

    /**
     * @param array $value
     * @param array $expected
     *
     * @dataProvider provideReverseTransformValue
     */
    public function testReverseTransform($value, $expected)
    {
        $this->assertEquals(serialize($expected), $this->transformer->reverseTransform($value));
    }

    /**
     * @return array
     */
    public function provideReverseTransformValue()
    {
        return array(
            array('( NOT ( 57459d3202b0cfdf088b4570 OR 57459d3202b0cfdf088b4571 ) AND 57459d3202b0cfdf088b4572 ) OR ( 57459d3202b0cfdf088b4573 OR 57459d3202b0cfdf088b4574 OR NOT 57459d3202b0cfdf088b4575 )',
                array (
                    '$or' =>
                        array (
                            array (
                                '$and' =>
                                    array (
                                        array (
                                            '$not' =>
                                                array (
                                                    '$or' =>
                                                        array (
                                                            array (
                                                                'keywords.$id' =>
                                                                    array (
                                                                        '$eq' => new \MongoId('57459d3202b0cfdf088b4570'),
                                                                    ),
                                                            ),
                                                            array (
                                                                'keywords.$id' =>
                                                                    array (
                                                                        '$eq' => new \MongoId('57459d3202b0cfdf088b4571'),
                                                                    ),
                                                            ),
                                                        ),
                                                ),
                                        ),
                                        array (
                                            'keywords.$id' =>
                                                array (
                                                    '$eq' => new \MongoId('57459d3202b0cfdf088b4572'),
                                                ),
                                        ),
                                    ),
                            ),
                            array (
                                '$or' =>
                                   array (
                                        array (
                                            'keywords.$id' =>
                                                array (
                                                    '$eq' => new \MongoId('57459d3202b0cfdf088b4573'),
                                                ),
                                        ),
                                        array (
                                            'keywords.$id' =>
                                                array (
                                                    '$eq' => new \MongoId('57459d3202b0cfdf088b4574'),
                                                ),
                                        ),
                                        array (
                                            'keywords.$id' =>
                                                array (
                                                    '$ne' => new \MongoId('57459d3202b0cfdf088b4575'),
                                                ),
                                        ),
                                    ),
                            ),
                        ),
                ),
            ),
            array('57459d3202b0cfdf088b4570',
                array (
                    '$and' =>
                        array (
                            array (
                                'keywords.$id' =>
                                    array (
                                        '$eq' => new \MongoId('57459d3202b0cfdf088b4570')
                                    )
                            )
                        )
                 )
            )
        );
    }

    /**
     * Test Exception reverseTransform
     *
     * @dataProvider provideReverseTransformException
     */
    public function testExceptionReverseTransform($value)
    {
        $this->setExpectedException('Symfony\Component\Form\Exception\TransformationFailedException');
        $this->transformer->reverseTransform($value);
    }

    /**
     * @return array
     */
    public function provideReverseTransformException()
    {
        return array(
            array('( cat:X1('),
            array('NOT NOT cat:X1'),
            array('cat:X1 AND'),
            array('cat:X1 AND '),
            array('cat:X1 AND AND cat:X2'),
        );
    }

    /**
     * @param array $expected
     * @param array $value
     *
     * @dataProvider provideTransformValue
     */
    public function testTransform($value, $expected)
    {
        $this->assertEquals($expected, $this->transformer->transform(serialize($value)));
    }

    /**
     * @return array
     */
    public function provideTransformValue()
    {
        return array(
            array(
                array (
                    '$or' =>
                        array (
                            array (
                                '$and' =>
                                    array (
                                        array (
                                            '$not' =>
                                                array (
                                                    '$or' =>
                                                        array (
                                                            array (
                                                                'keywords.$id' =>
                                                                    array (
                                                                        '$eq' => new \MongoId('57459d3202b0cfdf088b4570'),
                                                                    ),
                                                            ),
                                                            array (
                                                                'keywords.$id' =>
                                                                    array (
                                                                        '$eq' => new \MongoId('57459d3202b0cfdf088b4571'),
                                                                    ),
                                                            ),
                                                        ),
                                                ),
                                        ),
                                        array (
                                            'keywords.$id' =>
                                                array (
                                                    '$eq' => new \MongoId('57459d3202b0cfdf088b4572'),
                                                ),
                                        ),
                                    ),
                            ),
                            array (
                                '$or' =>
                                   array (
                                        array (
                                            'keywords.$id' =>
                                                array (
                                                    '$eq' => new \MongoId('57459d3202b0cfdf088b4573'),
                                                ),
                                        ),
                                        array (
                                            'keywords.$id' =>
                                                array (
                                                    '$eq' => new \MongoId('57459d3202b0cfdf088b4574'),
                                                ),
                                        ),
                                        array (
                                            'keywords.$id' =>
                                                array (
                                                    '$ne' => new \MongoId('57459d3202b0cfdf088b4575'),
                                                ),
                                        ),
                                    ),
                            ),
                        ),
                ),
                '( ( NOT ( 57459d3202b0cfdf088b4570 OR 57459d3202b0cfdf088b4571 ) AND 57459d3202b0cfdf088b4572 ) OR ( 57459d3202b0cfdf088b4573 OR 57459d3202b0cfdf088b4574 OR NOT 57459d3202b0cfdf088b4575 ) )',
            ),
            array(
                array (
                    '$and' =>
                        array (
                            array (
                                'keywords.$id' =>
                                    array (
                                        '$eq' => new \MongoId('57459d3202b0cfdf088b4570')
                                    )
                            )
                        )
                 ),
                '( 57459d3202b0cfdf088b4570 )',
            )
        );
    }
}

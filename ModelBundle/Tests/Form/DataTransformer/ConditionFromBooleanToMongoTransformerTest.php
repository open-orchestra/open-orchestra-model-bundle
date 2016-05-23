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
        $this->assertEquals($expected, $this->transformer->reverseTransform($value));
    }

    /**
     * @return array
     */
    public function provideReverseTransformValue()
    {
        return array(
            array('( NOT ( cat:X1 OR cat:X2 ) AND author:AAA ) OR ( T1 OR T2 OR NOT T3 )', '{"$or":[{"$and":[{"$not":{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]}},{"keywords":{"$eq":"author:AAA"}}]},{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}},{"keywords":{"$ne":"T3"}}]}]}'),
            array('( cat:X1 OR cat:X2 ) AND ( author:AAA ) AND ( T1 OR T2 OR NOT T3 )', '{"$and":[{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]},{"$and":[{"keywords":{"$eq":"author:AAA"}}]},{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}},{"keywords":{"$ne":"T3"}}]}]}'),
            array('cat:X1', '{"$and":[{"keywords":{"$eq":"cat:X1"}}]}'),
            array('( cat:X1 )', '{"$and":[{"$and":[{"keywords":{"$eq":"cat:X1"}}]}]}'),
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
     * @param array $value
     * @param array $expected
     *
     * @dataProvider provideTransformValue
     */
    public function testTransform($value, $expected)
    {
        $this->assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return array
     */
    public function provideTransformValue()
    {
        return array(
            array('{"$and":[{"$or":[{"keywords":{"$eq":"cat:X1"}},{"keywords":{"$eq":"cat:X2"}}]},{"keywords":{"$eq":"author:AAA"}},{"$and":[{"$or":[{"keywords":{"$eq":"T1"}},{"keywords":{"$eq":"T2"}}]},{"keywords":{"$ne":"T3"}}]}]}', '( ( cat:X1 OR cat:X2 ) AND author:AAA AND ( ( T1 OR T2 ) AND NOT T3 ) )'),
            array('{"keywords":{"$eq":"cat:X1"}}', 'cat:X1'),
        );
    }
}

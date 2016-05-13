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
    protected $keywordRepository;
    protected $keywordToDocumentManager;

    public function setUp()
    {
        $catX1Keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($catX1Keyword)->getLabel()->thenReturn('cat:X1');
        Phake::when($catX1Keyword)->getId()->thenReturn('fakeId[cat:X1]');
        $catX2Keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($catX2Keyword)->getLabel()->thenReturn('cat:X2');
        Phake::when($catX2Keyword)->getId()->thenReturn('fakeId[cat:X2]');
        $authorAAAKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($authorAAAKeyword)->getLabel()->thenReturn('author:AAA');
        Phake::when($authorAAAKeyword)->getId()->thenReturn('fakeId[author:AAA]');
        $t1Keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($t1Keyword)->getLabel()->thenReturn('T1');
        Phake::when($t1Keyword)->getId()->thenReturn('fakeId[T1]');
        $t2Keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($t2Keyword)->getLabel()->thenReturn('T2');
        Phake::when($t2Keyword)->getId()->thenReturn('fakeId[T2]');
        $t3Keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($t3Keyword)->getLabel()->thenReturn('T3');
        Phake::when($t3Keyword)->getId()->thenReturn('fakeId[T3]');

        $this->keywordRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface');
        Phake::when($this->keywordRepository)->find('fakeId[cat:X1]')->thenReturn($catX1Keyword);
        Phake::when($this->keywordRepository)->find('fakeId[cat:X2]')->thenReturn($catX2Keyword);
        Phake::when($this->keywordRepository)->find('fakeId[author:AAA]')->thenReturn($authorAAAKeyword);
        Phake::when($this->keywordRepository)->find('fakeId[T1]')->thenReturn($t1Keyword);
        Phake::when($this->keywordRepository)->find('fakeId[T2]')->thenReturn($t2Keyword);
        Phake::when($this->keywordRepository)->find('fakeId[T3]')->thenReturn($t3Keyword);

        $this->keywordToDocumentManager = Phake::mock('OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager');
        Phake::when($this->keywordToDocumentManager)->getDocument('cat:X1')->thenReturn($catX1Keyword);
        Phake::when($this->keywordToDocumentManager)->getDocument('cat:X2')->thenReturn($catX2Keyword);
        Phake::when($this->keywordToDocumentManager)->getDocument('author:AAA')->thenReturn($authorAAAKeyword);
        Phake::when($this->keywordToDocumentManager)->getDocument('T1')->thenReturn($t1Keyword);
        Phake::when($this->keywordToDocumentManager)->getDocument('T2')->thenReturn($t2Keyword);
        Phake::when($this->keywordToDocumentManager)->getDocument('T3')->thenReturn($t3Keyword);

        $this->transformer = new ConditionFromBooleanToMongoTransformer($this->keywordToDocumentManager, $this->keywordRepository);

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
            array('( NOT ( cat:X1 OR cat:X2 ) AND author:AAA ) OR ( T1 OR T2 OR NOT T3 )', '{"$or":[{"$and":[{"$not":{"$or":[{"keywords":{"$eq":"fakeId[cat:X1]"}},{"keywords":{"$eq":"fakeId[cat:X2]"}}]}},{"keywords":{"$eq":"fakeId[author:AAA]"}}]},{"$or":[{"keywords":{"$eq":"fakeId[T1]"}},{"keywords":{"$eq":"fakeId[T2]"}},{"keywords":{"$ne":"fakeId[T3]"}}]}]}'),
            array('( cat:X1 OR cat:X2 ) AND ( author:AAA ) AND ( T1 OR T2 OR NOT T3 )', '{"$and":[{"$or":[{"keywords":{"$eq":"fakeId[cat:X1]"}},{"keywords":{"$eq":"fakeId[cat:X2]"}}]},{"$and":[{"keywords":{"$eq":"fakeId[author:AAA]"}}]},{"$or":[{"keywords":{"$eq":"fakeId[T1]"}},{"keywords":{"$eq":"fakeId[T2]"}},{"keywords":{"$ne":"fakeId[T3]"}}]}]}'),
            array('cat:X1', '{"$and":[{"keywords":{"$eq":"fakeId[cat:X1]"}}]}'),
            array('( cat:X1 )', '{"$and":[{"$and":[{"keywords":{"$eq":"fakeId[cat:X1]"}}]}]}'),
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
            array('{"$and":[{"$or":[{"keywords":{"$eq":"fakeId[cat:X1]"}},{"keywords":{"$eq":"fakeId[cat:X2]"}}]},{"keywords":{"$eq":"fakeId[author:AAA]"}},{"$and":[{"$or":[{"keywords":{"$eq":"fakeId[T1]"}},{"keywords":{"$eq":"fakeId[T2]"}}]},{"keywords":{"$ne":"fakeId[T3]"}}]}]}', '( ( cat:X1 OR cat:X2 ) AND author:AAA AND ( ( T1 OR T2 ) AND NOT T3 ) )'),
            array('{"keywords":{"$eq":"fakeId[cat:X1]"}}', 'cat:X1'),
        );
    }
}

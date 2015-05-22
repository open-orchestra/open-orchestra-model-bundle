<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueFieldIdContentTypeValidator;
use Phake;

/**
 * Class UniqueFieldIdContentTypeValidatorTest
 */
class UniqueFieldIdContentTypeValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UniqueFieldIdContentTypeValidator
     */
    protected $validator;

    protected $constraint;
    protected $context;
    protected $field2;
    protected $field3;
    protected $contentType;
    protected $fieldId = 'fakeId';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = Phake::mock('Symfony\Component\Validator\Constraint');
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($constraintViolationBuilder);
        Phake::when($constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($constraintViolationBuilder);

        $field = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field)->getFieldId()->thenReturn($this->fieldId);
        $this->field2 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        $this->field3 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');

        $fields = array();
        $fields[] = $field;
        $fields[] = $this->field2;
        $fields[] = $this->field3;

        $this->contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($this->contentType)->getFields()->thenReturn($fields);

        $this->validator = new UniqueFieldIdContentTypeValidator();
        $this->validator->initialize($this->context);
    }

    /**
     * Test instance
     */
    public function testClass()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $this->validator);
    }

    /**
     * @param string  $fieldId
     * @param string  $fieldId2
     * @param integer $violationTimes
     *
     * @dataProvider provideFieldIdAndViolation
     */
    public function testValidate($fieldId, $fieldId2, $violationTimes)
    {
        Phake::when($this->field2)->getFieldId()->thenReturn($fieldId);
        Phake::when($this->field3)->getFieldId()->thenReturn($fieldId2);

        $this->validator->validate($this->contentType, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideFieldIdAndViolation()
    {
        return array(
            array('fakeId2','fakeId3', 0),
            array('fakeId','fakeId3', 1),
            array('fakeId','fakeId', 2),
        );
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use Phake;
use OpenOrchestra\ModelBundle\Validator\Constraints\IdAuthorizedCharacter;
use OpenOrchestra\ModelBundle\Validator\Constraints\IdAuthorizedCharacterValidator;

/**
 * Class IdAuthorizedCharacterValidatorTest
 */
class IdAuthorizedCharacterValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IdAuthorizedCharacterValidator
     */
    protected $validator;

    protected $context;
    protected $constraint;
    protected $constraintViolationBuilder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = Phake::mock('OpenOrchestra\ModelBundle\Validator\Constraints\IdAuthorizedCharacter');
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->validator = new IdAuthorizedCharacterValidator();
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
     * @param string $value
     * @param int    $violationTimes
     *
     * @dataProvider provideCountAndViolation
     */
    public function testAddViolationOrNot($value, $violationTimes)
    {
        $this->validator->validate($value, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation($this->constraint->message);
        Phake::verify($this->constraintViolationBuilder, Phake::times($violationTimes));
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        return array(
            array('', 1),
            array('test,test', 1),
            array('mon_content_type_id', 0),
        );
    }
}

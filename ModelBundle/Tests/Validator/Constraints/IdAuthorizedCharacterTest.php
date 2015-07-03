<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\IdAuthorizedCharacter;
use Symfony\Component\Validator\Constraint;

/**
 * Class IdAuthorizedCharacterTest
 */
class IdAuthorizedCharacterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IdAuthorizedCharacter
     */
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new IdAuthorizedCharacter();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\Constraint', $this->constraint);
    }

    /**
     * Test validateBy
     */
    public function testValidateBy()
    {
        $this->assertSame('id_authorized_character', $this->constraint->validatedBy());
    }

    /**
     * test target
     */
    public function testTarget()
    {
        $this->assertSame(Constraint::PROPERTY_CONSTRAINT, $this->constraint->getTargets());
    }

    /**
     * test message
     */
    public function testMessages()
    {
        $this->assertSame('open_orchestra_model_validators.field.special_character', $this->constraint->message);
    }
}

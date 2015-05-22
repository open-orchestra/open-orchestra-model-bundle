<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueContentTypeId;
use Symfony\Component\Validator\Constraint;

/**
 * Test UniqueContentTypeIdTest
 */
class UniqueContentTypeIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UniqueContentTypeId
     */
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueContentTypeId();
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
        $this->assertSame('unique_content_type_id', $this->constraint->validatedBy());
    }

    /**
     * test target
     */
    public function testTarget()
    {
        $this->assertSame(Constraint::CLASS_CONSTRAINT, $this->constraint->getTargets());
    }

    /**
     * test message
     */
    public function testMessages()
    {
        $this->assertSame('open_orchestra_model_validators.document.content_type.unique_content_type_id', $this->constraint->message);
    }
}

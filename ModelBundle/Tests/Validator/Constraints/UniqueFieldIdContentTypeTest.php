<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueFieldIdContentType;
use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueFieldIdContentTypeTest
 */
class UniqueFieldIdContentTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UniqueFieldIdContentType
     */
    protected $constraint;

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->constraint = new UniqueFieldIdContentType();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\Constraint', $this->constraint);
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
        $this->assertSame('open_orchestra_model_validators.document.content_type.unique_field_id', $this->constraint->message);
    }
}

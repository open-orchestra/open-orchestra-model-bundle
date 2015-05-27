<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\CheckAreaPresence;
use Symfony\Component\Validator\Constraint;

/**
 * Class CheckAreaPresenceTest
 */
class CheckAreaPresenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CheckAreaPresence
     */
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new CheckAreaPresence();
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
        $this->assertSame('open_orchestra_model_validators.document.area.presence_required', $this->constraint->message);
    }
}

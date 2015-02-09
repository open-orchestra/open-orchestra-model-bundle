<?php

namespace PHPOrchestra\ModelBundle\Test\Validator\Constraints;

use PHPOrchestra\ModelBundle\Validator\Constraints\CheckRoutePattern;
use Symfony\Component\Validator\Constraint;

/**
 * Class CheckRoutePatternTest
 */
class CheckRoutePatternTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CheckRoutePattern
     */
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new CheckRoutePattern();
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
        $this->assertSame('check_route_pattern', $this->constraint->validatedBy());
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
        $this->assertSame('php_orchestra_model.node.check_route_pattern', $this->constraint->message);
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Test\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueMainAlias;
use Symfony\Component\Validator\Constraint;

/**
 * Test UniqueMainAliasTest
 */
class UniqueMainAliasTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var UniqueMainAlias
     */
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueMainAlias();
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
        $this->assertSame('unique_main_alias', $this->constraint->validatedBy());
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
        $this->assertSame('open_orchestra_model.website.unique_main_alias', $this->constraint->message);
    }
}

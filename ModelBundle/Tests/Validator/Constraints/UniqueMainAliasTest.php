<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

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
        $this->assertSame('open_orchestra_model_validators.document.website.unique_main_alias', $this->constraint->message);
    }
}

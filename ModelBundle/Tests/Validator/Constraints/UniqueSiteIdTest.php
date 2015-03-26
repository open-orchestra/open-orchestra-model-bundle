<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueSiteId;
use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueSiteIdTest
 */
class UniqueSiteIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UniqueSiteId
     */
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueSiteId();
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
        $this->assertSame('unique_site_id', $this->constraint->validatedBy());
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
        $this->assertSame('open_orchestra_model.website.unique_site_id', $this->constraint->message);
    }
}

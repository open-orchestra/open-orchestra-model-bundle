<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\CheckRoutePattern;
use Symfony\Component\Validator\Constraint;

/**
 * Class CheckRoutePatternTest
 */
class CheckRoutePatternTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new CheckRoutePattern();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'check_route_pattern',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_model_validators.document.node.check_route_pattern'
        );
        $this->assertSame('open_orchestra_model_validators.document.node.check_route_pattern_node_deleted', $this->constraint->messageWitNodeDeleted);
    }
}

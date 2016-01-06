<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueNodeOrder;
use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueNodeOrderTest
 */
class UniqueNodeOrderTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueNodeOrder();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'unique_node_order',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_model_validators.document.node.unique_node_order'
        );
    }
}

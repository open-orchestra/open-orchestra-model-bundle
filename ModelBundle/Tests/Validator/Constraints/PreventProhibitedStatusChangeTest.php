<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\PreventProhibitedStatusChange;
use Symfony\Component\Validator\Constraint;

/**
 * Class PreventProhibitedStatusChangeTest
 */
class PreventProhibitedStatusChangeTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new PreventProhibitedStatusChange();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'prevent_prohibited_status_change',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_model_validators.document.status.impossible_change'
        );
    }
}

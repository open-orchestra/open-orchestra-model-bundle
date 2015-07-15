<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\CheckAreaPresence;
use Symfony\Component\Validator\Constraint;

/**
 * Class CheckAreaPresenceTest
 */
class CheckAreaPresenceTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new CheckAreaPresence();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'OpenOrchestra\ModelBundle\Validator\Constraints\CheckAreaPresenceValidator',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_model_validators.document.area.presence_required'
        );
    }
}

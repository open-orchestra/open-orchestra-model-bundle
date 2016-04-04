<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\CheckMainAliasPresence;
use Symfony\Component\Validator\Constraint;

/**
 * Class CheckMainAliasPresenceTest
 */
class CheckMainAliasPresenceTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new CheckMainAliasPresence();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'OpenOrchestra\ModelBundle\Validator\Constraints\CheckMainAliasPresenceValidator',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_model_validators.document.website.exists_main_alias'
        );
    }
}

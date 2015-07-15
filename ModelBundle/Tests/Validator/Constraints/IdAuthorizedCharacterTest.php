<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\IdAuthorizedCharacter;
use Symfony\Component\Validator\Constraint;

/**
 * Class IdAuthorizedCharacterTest
 */
class IdAuthorizedCharacterTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new IdAuthorizedCharacter();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'id_authorized_character',
            Constraint::PROPERTY_CONSTRAINT,
            'open_orchestra_model_validators.field.special_character'
        );
    }
}

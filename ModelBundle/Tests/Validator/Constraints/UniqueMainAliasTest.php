<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueMainAlias;
use Symfony\Component\Validator\Constraint;

/**
 * Test UniqueMainAliasTest
 */
class UniqueMainAliasTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueMainAlias();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'OpenOrchestra\ModelBundle\Validator\Constraints\UniqueMainAliasValidator',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_model_validators.document.website.unique_main_alias'
        );
    }
}

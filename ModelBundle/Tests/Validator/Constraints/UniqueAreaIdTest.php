<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueAreaId;
use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueAreaIdTest
 */
class UniqueAreaIdTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueAreaId();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'unique_area_id',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_model_validators.document.area.unique_area_id'
        );
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueContentTypeId;
use Symfony\Component\Validator\Constraint;

/**
 * Test UniqueContentTypeIdTest
 */
class UniqueContentTypeIdTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueContentTypeId();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'unique_content_type_id',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_model_validators.document.content_type.unique_content_type_id'
        );
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueFieldIdContentType;
use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueFieldIdContentTypeTest
 */
class UniqueFieldIdContentTypeTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->constraint = new UniqueFieldIdContentType();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'OpenOrchestra\ModelBundle\Validator\Constraints\UniqueFieldIdContentTypeValidator',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_model_validators.document.content_type.unique_field_id'
        );
    }
}

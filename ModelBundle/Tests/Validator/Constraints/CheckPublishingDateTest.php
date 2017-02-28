<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\CheckPublishingDate;
use Symfony\Component\Validator\Constraint;

/**
 * Class CheckPublishingDateTest
 */
class CheckPublishingDateTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new CheckPublishingDate();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'check_publishing_date',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_model_validators.document.autopublish.check_publishing_date'
        );
    }
}

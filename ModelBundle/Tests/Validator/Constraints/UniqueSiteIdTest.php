<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\UniqueSiteId;
use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueSiteIdTest
 */
class UniqueSiteIdTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueSiteId();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'unique_site_id',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_model_validators.document.website.unique_site_id'
        );
    }
}

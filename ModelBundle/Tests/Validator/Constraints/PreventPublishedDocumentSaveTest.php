<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\ModelBundle\Validator\Constraints\PreventPublishedDocumentSave;
use Symfony\Component\Validator\Constraint;

/**
 * Class PreventPublishedDocumentSaveTest
 */
class PreventPublishedDocumentSaveTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new PreventPublishedDocumentSave();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'prevent_published_document_save',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_model.document.impossible_save'
        );
    }
}

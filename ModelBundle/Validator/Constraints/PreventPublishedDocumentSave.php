<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class PreventPublishedDocumentSave
 *
 * @deprecated use AuthorizeEdition constraint
 */
class PreventPublishedDocumentSave extends Constraint
{
    public $message = 'open_orchestra_model.document.impossible_save';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'prevent_published_document_save';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

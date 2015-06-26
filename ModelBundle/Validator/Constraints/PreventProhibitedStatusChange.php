<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class PreventProhibitedStatusChange
 * @Annotation
 */
class PreventProhibitedStatusChange extends Constraint
{
    public $message = 'open_orchestra_model_validators.document.status.impossible_change';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'prevent_prohibited_status_change';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

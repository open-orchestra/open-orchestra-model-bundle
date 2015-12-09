<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CheckAreaPresence
 *
 * @deprecated use the Constraint in the BackofficeBundle, will be removed in 1.2.0
 */
class CheckAreaPresence extends Constraint
{
    public $message = 'open_orchestra_model_validators.document.area.presence_required';

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CheckAreaPresence
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

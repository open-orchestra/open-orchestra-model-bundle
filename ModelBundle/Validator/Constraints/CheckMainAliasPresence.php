<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class CheckMainAliasPresence extends Constraint
{
    public $message = 'open_orchestra_model_validators.document.website.exists_main_alias';

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

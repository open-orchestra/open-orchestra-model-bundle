<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueMainAlias
 * @Annotation
 */
class UniqueMainAlias extends Constraint
{
    public $message = 'open_orchestra_model_validators.document.website.unique_main_alias';

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

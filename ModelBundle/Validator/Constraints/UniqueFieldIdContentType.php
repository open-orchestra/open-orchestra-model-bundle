<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueFieldIdContentType
 */
class UniqueFieldIdContentType extends Constraint
{
    public $message = 'open_orchestra_model_validators.document.content_type.unique_field_id';

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
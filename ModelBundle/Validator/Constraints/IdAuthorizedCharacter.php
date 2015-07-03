<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class IdAuthorizedCharacter
 */
class IdAuthorizedCharacter extends Constraint
{
    public $message = 'open_orchestra_model_validators.field.special_character';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'id_authorized_character';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}

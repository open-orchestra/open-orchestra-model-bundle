<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class IdAuthorizedCharacterValidator
 */
class IdAuthorizedCharacterValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param string     $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/^[a-zA-Z0-9_][a-zA-Z0-9_\-:]*$/', $value, $matches)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}

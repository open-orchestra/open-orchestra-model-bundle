<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckMainAliasPresenceValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param SiteInterface $value The value that should be validated
     * @param Constraint    $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value->getMainAlias()->isMain()) {
            $this->context->buildViolation($constraint->message)
                          ->addViolation();
        }
    }
}

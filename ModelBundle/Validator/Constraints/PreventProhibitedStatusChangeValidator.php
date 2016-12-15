<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class PreventProhibitedStatusChangeValidator
 */
class PreventProhibitedStatusChangeValidator extends ConstraintValidator
{
    protected $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof StatusableInterface) {
            return;
        }

        $status = $value->getStatus();
        if (!$status instanceof StatusInterface) {
            return;
        }

        if (! $this->authorizationChecker->isGranted($status, $value)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('status')
                ->addViolation();
        }
    }
}

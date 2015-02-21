<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CheckAreaPresenceValidator
 */
class CheckAreaPresenceValidator extends ConstraintValidator
{
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param NodeInterface $value The value that should be validated
     * @param Constraint    $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (0 === $value->getAreas()->count()) {
            $this->context->addViolationAt('nodeSource', $this->translator->trans($constraint->message));
            $this->context->addViolationAt('templateId', $this->translator->trans($constraint->message));
        }
    }
}

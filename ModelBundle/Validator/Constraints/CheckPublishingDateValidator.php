<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\AutoPublishableInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CheckPublishingDateValidator
 */
class CheckPublishingDateValidator extends ConstraintValidator
{
    protected $nodeRepository;

    /**
     * Checks if the passed value is valid.
     *
     * @param AutoPublishableInterface $value The value that should be validated
     * @param Constraint               $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof AutoPublishableInterface &&
            $value->getPublishDate() instanceof \DateTime &&
            $value->getUnpublishDate() instanceof \DateTime &&
            $value->getPublishDate()->getTimestamp() > $value->getUnpublishDate()->getTimestamp()
        ) {
            $this->context->buildViolation($constraint->message)
                ->atPath('unpublishDate')
                ->addViolation();
        }
    }
}

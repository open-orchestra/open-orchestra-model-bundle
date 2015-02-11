<?php

namespace PHPOrchestra\ModelBundle\Validator\Constraints;

use PHPOrchestra\ModelInterface\Model\SiteAliasInterface;
use PHPOrchestra\ModelInterface\Model\SiteInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueMainAliasValidator
 */
class UniqueMainAliasValidator extends ConstraintValidator
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
     * @param SiteInterface              $value      The value that should be validated
     * @param UniqueMainAlias|Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value->getAliases()->filter(function(SiteAliasInterface $alias) {
            return $alias->isMain();
        })->count() > 1) {
            $this->context->addViolation($this->translator->trans($constraint->message));
        }
    }

}

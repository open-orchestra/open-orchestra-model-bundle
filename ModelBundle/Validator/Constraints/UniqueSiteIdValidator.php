<?php

namespace PHPOrchestra\ModelBundle\Validator\Constraints;

use PHPOrchestra\ModelInterface\Model\SiteInterface;
use PHPOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueSiteIdValidator
 */
class UniqueSiteIdValidator extends ConstraintValidator
{
    protected $translator;
    protected $repository;

    /**
     * @param TranslatorInterface $translator
     * @param SiteRepositoryInterface $repository
     */
    public function __construct(TranslatorInterface $translator, SiteRepositoryInterface $repository)
    {
        $this->translator = $translator;
        $this->repository = $repository;
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
        $result = $this->repository->findOneBySiteId($value->getSiteId());

        if (null !== $result && $result->getId() !== $value->getId()) {
            $this->context->addViolationAt('siteId', $this->translator->trans($constraint->message));
        }
    }
}

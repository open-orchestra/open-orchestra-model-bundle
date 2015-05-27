<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueSiteIdValidator
 */
class UniqueSiteIdValidator extends ConstraintValidator
{
    protected $repository;

    /**
     * @param SiteRepositoryInterface $repository
     */
    public function __construct(SiteRepositoryInterface $repository)
    {
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
            $this->context->addViolationAt('siteId', $constraint->message);
        }
    }
}

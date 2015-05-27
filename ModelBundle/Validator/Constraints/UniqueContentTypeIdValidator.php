<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class UniqueContentTypeValidator
 */
class UniqueContentTypeIdValidator extends ConstraintValidator
{
    protected $repository;

    /**
     * @param ContentTypeRepositoryInterface $repository
     */
    public function __construct(ContentTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ContentTypeInterface           $value
     * @param UniqueContentTypeId|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $result = $this->repository->findOneByContentTypeIdInLastVersion($value->getContentTypeId());

        if (null !== $result && $value->getVersion() <= 1) {
            $this->context->buildViolation($constraint->message)
                ->atPath('contentTypeId')
                ->addViolation();
        }
    }
}

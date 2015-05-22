<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use OpenOrchestra\ModelBundle\Repository\ContentTypeRepository;
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
     * @param ContentTypeRepository $repository
     */
    public function __construct(ContentTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ContentTypeInterface           $value
     * @param UniqueContentTypeId|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $result = $this->repository->findOneByContentTypeId($value->getContentTypeId());

        if (null !== $result && $result->getId() !== $value->getId()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('contentTypeId')
                ->addViolation();
        }
    }
}

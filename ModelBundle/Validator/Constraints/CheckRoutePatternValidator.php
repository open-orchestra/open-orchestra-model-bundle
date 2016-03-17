<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CheckRoutePatternValidator
 */
class CheckRoutePatternValidator extends ConstraintValidator
{
    protected $nodeRepository;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(NodeRepositoryInterface $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param NodeInterface $value The value that should be validated
     * @param Constraint    $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $nodesSameRoute = $this->nodeRepository->findByParentAndRoutePattern(
            $value->getParentId(),
            $value->getRoutePattern(),
            $value->getNodeId(),
            $value->getSiteId()
        );
        if (0 < count($nodesSameRoute)) {
            $nodesSameRoute = current($nodesSameRoute);
            $message = $constraint->message;
            if (true === $nodesSameRoute->isDeleted()) {
                $message = $constraint->messageWitNodeDeleted;
            }
            $this->context->buildViolation($message, array("%nodeName%" => $nodesSameRoute->getName()))
                ->atPath('routePattern')
                ->addViolation();
        }
    }
}

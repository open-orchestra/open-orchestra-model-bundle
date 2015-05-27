<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Translation\TranslatorInterface;
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
        if (0 < count($this->nodeRepository->findByParentIdAndRoutePatternAndNotNodeIdAndSiteId(
                $value->getParentId(),
                $value->getRoutePattern(),
                $value->getNodeId(),
                $value->getSiteId()
            ))) {
            $this->context->addViolationAt('routePattern', $constraint->message);
        }
    }
}

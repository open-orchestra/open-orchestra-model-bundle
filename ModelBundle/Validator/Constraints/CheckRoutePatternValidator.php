<?php

namespace PHPOrchestra\ModelBundle\Validator\Constraints;

use PHPOrchestra\ModelInterface\Model\NodeInterface;
use PHPOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CheckRoutePatternValidator
 */
class CheckRoutePatternValidator extends ConstraintValidator
{
    protected $translator;
    protected $nodeRepository;

    /**
     * @param TranslatorInterface     $translator
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(TranslatorInterface $translator, NodeRepositoryInterface $nodeRepository)
    {
        $this->translator = $translator;
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
        if (0 < count($this->nodeRepository->findByParentIdAndRoutePatternAndNotNodeId($value->getParentId(), $value->getRoutePattern(), $value->getNodeId()))) {
            $this->context->addViolationAt('routePattern', $this->translator->trans($constraint->message));
        }
    }
}

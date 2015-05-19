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
    protected $translator;
    protected $nodeRepository;
    protected $currentSiteManager;

    /**
     * @param TranslatorInterface     $translator
     * @param NodeRepositoryInterface $nodeRepository
     * @param CurrentSiteIdInterface  $currentSiteManager
     */
    public function __construct(TranslatorInterface $translator, NodeRepositoryInterface $nodeRepository, CurrentSiteIdInterface $currentSiteManager)
    {
        $this->translator = $translator;
        $this->nodeRepository = $nodeRepository;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param NodeInterface $value The value that should be validated
     * @param Constraint    $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $siteId = $this->currentSiteManager->getCurrentSiteId();
        if (0 < count($this->nodeRepository->findByParentIdAndRoutePatternAndNotNodeIdAndSiteId($value->getParentId(), $value->getRoutePattern(), $value->getNodeId(), $siteId))) {
            $this->context->addViolationAt('routePattern', $this->translator->trans($constraint->message));
        }
    }
}

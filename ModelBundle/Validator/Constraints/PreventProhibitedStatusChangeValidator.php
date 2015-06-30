<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\ModelBundle\Repository\RoleRepository;

/**
 * Class PreventProhibitedStatusChangeValidator
 */
class PreventProhibitedStatusChangeValidator extends ConstraintValidator
{
    protected $securityContext;
    protected $documentManager;
    protected $roleRepository;

    /**
     * @param AuthorizationCheckerInterface $securityContext
     * @param DocumentManager               $documentManager
     * @param RoleRepository                $roleRepository
     */
    public function __construct(
        AuthorizationCheckerInterface $securityContext,
        DocumentManager $documentManager,
        RoleRepository $roleRepository
    )
    {
        $this->securityContext = $securityContext;
        $this->documentManager = $documentManager;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $oldNode = $this->documentManager->getUnitOfWork()->getOriginalDocumentData($value);
        if (empty($oldNode)) {
            return;
        }

        $oldStatus = $oldNode['status'];
        $status = $value->getStatus();

        if ($oldStatus->getId() == $status->getId()) {
            return;
        }

        if (! $this->canSwitchStatus($oldStatus, $status)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('status')
                ->addViolation();
        }
    }

    /**
     * Check if current user is allowed to change content/node from fromStatus to toStatus
     * 
     * @param StatusInterface $fromStatus
     * @param StatusInterface $toStatus
     * 
     * @return boolean
     */
    public function canSwitchStatus($fromStatus, $toStatus)
    {
        $role = $this->roleRepository->findOneByFromStatusAndToStatus($fromStatus, $toStatus);

        if ($role)
            return $this->securityContext->isGranted($role->getName());

        return false;
    }
}

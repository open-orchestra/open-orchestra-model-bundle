<?php

namespace OpenOrchestra\ModelBundle\Validator\Constraints;

use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class PreventProhibitedStatusChangeValidator
 */
class PreventProhibitedStatusChangeValidator extends ConstraintValidator
{
    protected $authorizationChecker;
    protected $objectManager;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param DocumentManager                $objectManager
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, DocumentManager $objectManager)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->objectManager = $objectManager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof StatusableInterface) {
            return;
        }

        $originalDoc = $this->objectManager->getUnitOfWork()->getOriginalDocumentData($value);
        if (empty($originalDoc)) {
            return ;
        }

        $status = $value->getStatus();
        $oldStatus = $originalDoc['status'];

        if (!$status instanceof StatusInterface || !$oldStatus instanceof StatusInterface) {
            return;
        }

        if ($oldStatus->getId() === $status->getId()) {
            return;
        }

        $oldNode = clone $value;
        $this->objectManager->detach($oldNode);
        $oldNode->setStatus($originalDoc['status']);

        if (!$this->authorizationChecker->isGranted($status, $oldNode)) {
            echo 'pas autorisé';
            $this->context->buildViolation($constraint->message)
                ->atPath('status')
                ->addViolation();
        }
    }
}

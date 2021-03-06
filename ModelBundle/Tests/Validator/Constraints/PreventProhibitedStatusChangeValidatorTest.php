<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ModelBundle\Validator\Constraints\PreventProhibitedStatusChange;
use OpenOrchestra\ModelBundle\Validator\Constraints\PreventProhibitedStatusChangeValidator;

/**
 * Class PreventProhibitedStatusChangeValidatorTest
 */
class PreventProhibitedStatusChangeValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var PreventProhibitedStatusChangeValidator
     */
    protected $validator;
    protected $authorizationChecker;

    protected $documentManager;
    protected $oldRoleName;
    protected $constraint;
    protected $constraintViolationBuilder;
    protected $unitOfWork;
    protected $oldStatus;
    protected $oldNode;
    protected $context;
    protected $status;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->constraint = new PreventProhibitedStatusChange();
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node)->getStatus()->thenReturn($this->status);

        $this->oldStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->oldStatus)->getId()->thenReturn('oldId');
        $this->oldNode = array('status' => $this->oldStatus);

        $this->unitOfWork = Phake::mock('Doctrine\ODM\MongoDB\UnitOfWork');
        Phake::when($this->unitOfWork)->getOriginalDocumentData(Phake::anyParameters())->thenReturn($this->oldNode);
        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        Phake::when($this->documentManager)->getUnitOfWork()->thenReturn($this->unitOfWork);

        $this->validator = new PreventProhibitedStatusChangeValidator(
            $this->authorizationChecker,
            $this->documentManager
        );
        $this->validator->initialize($this->context);
    }

    /**
     * Test add violation without right
     *
     * @param bool $isGranted
     * @param int  $numberOfViolation
     *
     * @dataProvider provideGrantResponse
     */
    public function testAddViolationOrNot($isGranted, $numberOfViolation)
    {
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn($isGranted);

        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->context, Phake::times($numberOfViolation))->buildViolation($this->constraint->message);
        Phake::verify($this->constraintViolationBuilder, Phake::times($numberOfViolation))->atPath('status');
    }

    /**
     * @return array
     */
    public function provideGrantResponse()
    {
        return array(
            array(true, 0),
            array(false, 1)
        );
    }
}

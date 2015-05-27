<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use Phake;
use OpenOrchestra\ModelBundle\Validator\Constraints\PreventProhibitedStatusChange;
use OpenOrchestra\ModelBundle\Validator\Constraints\PreventProhibitedStatusChangeValidator;

/**
 * Class PreventProhibitedStatusChangeValidatorTest
 */
class PreventProhibitedStatusChangeValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PreventProhibitedStatusChangeValidator
     */
    protected $validator;

    protected $roleRepository;
    protected $securityContext;
    protected $documentManager;
    protected $oldRoleName;
    protected $constraint;
    protected $unitOfWork;
    protected $oldStatus;
    protected $roleName;
    protected $oldRole;
    protected $oldNode;
    protected $context;
    protected $status;
    protected $role;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->securityContext = Phake::mock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->constraint = new PreventProhibitedStatusChange();
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContext');

        $this->roleName = 'ROLE';
        $this->role = Phake::mock('OpenOrchestra\ModelBundle\Document\Role');
        Phake::when($this->role)->getName()->thenReturn($this->roleName);
        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->status)->getId()->thenReturn('newId');

        $this->roleRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\RoleRepository');
        Phake::when($this->roleRepository)->findOneByFromStatusAndToStatus(Phake::anyParameters())->thenReturn($this->role);

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node)->getStatus()->thenReturn($this->status);

        $this->oldRoleName = 'OLD_ROLE';
        $this->oldRole = Phake::mock('OpenOrchestra\ModelBundle\Document\Role');
        Phake::when($this->oldRole)->getName()->thenReturn($this->oldRoleName);
        $this->oldStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->oldStatus)->getId()->thenReturn('oldId');

        $this->oldNode = array('status' => $this->oldStatus);

        $this->unitOfWork = Phake::mock('Doctrine\ODM\MongoDB\UnitOfWork');
        Phake::when($this->unitOfWork)->getOriginalDocumentData(Phake::anyParameters())->thenReturn($this->oldNode);
        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        Phake::when($this->documentManager)->getUnitOfWork()->thenReturn($this->unitOfWork);

        $this->validator = new PreventProhibitedStatusChangeValidator(
            $this->securityContext,
            $this->documentManager,
            $this->roleRepository
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
        Phake::when($this->securityContext)->isGranted($this->roleName)->thenReturn($isGranted);

        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->securityContext, Phake::atMost(1))->isGranted($this->roleName);
        Phake::verify($this->context, Phake::times($numberOfViolation))->addViolationAt('status', $this->constraint->message);
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

    /**
     * Test on node creation
     */
    public function testWhenNoOldNode()
    {
        Phake::when($this->unitOfWork)->getOriginalDocumentData(Phake::anyParameters())->thenReturn(array());

        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->context, Phake::never())->addViolationAt(Phake::anyParameters());
    }

    /**
     * Test on node creation
     */
    public function testWhenStatusTheSame()
    {
        Phake::when($this->status)->getId()->thenReturn('newId');
        Phake::when($this->oldStatus)->getId()->thenReturn('newId');

        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->context, Phake::never())->addViolationAt(Phake::anyParameters());
    }

    /**
     * Test if role exist
     * 
     * @param boolean $roleFound
     * @param boolean $isGranted
     * @param boolean $canSwitch
     *
     * @dataProvider provideRoleAndResult
     */
    public function testCanSwitchStatus($roleFound, $isGranted, $canSwitch)
    {
        if ($roleFound) {
            Phake::when($this->roleRepository)->findOneByFromStatusAndToStatus(Phake::anyParameters())->thenReturn($this->role);
            Phake::when($this->securityContext)->isGranted($this->roleName)->thenReturn($isGranted);
        } else {
            Phake::when($this->roleRepository)->findOneByFromStatusAndToStatus(Phake::anyParameters())->thenReturn(null);
        }

        $result = $this->validator->canSwitchStatus($this->oldNode['status'], $this->status);

        $this->assertEquals($canSwitch, $result);
    }

    /**
     * @return array
     */
    public function provideRoleAndResult()
    {
        return array(
            array(true, true, true),
            array(true, false, false),
            array(false, true, false),
            array(false, false, false)
        );
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Tests\Form\Type;

use OpenOrchestra\ModelBundle\Form\Type\WorkflowRoleChoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use Phake;

/**
 * Description of WorkflowRoleChoiceTypeTest
 */
class WorkflowRoleChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $roles;
    protected $roleClass = 'RoleClass';
    protected $roleRepositoryInterface;
    protected $form;
    
    /**
     * Set up the test
     */
    public function setUp()
    {
        $role1 = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');
        $role2 = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');

        $this->roles = new ArrayCollection();
        $this->roles->add($role1);
        $this->roles->add($role2);
        $this->roleRepositoryInterface = Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface');
        Phake::when($this->roleRepositoryInterface)->findWorkflowRole()->thenReturn($this->roles);

        $this->form = new WorkflowRoleChoiceType($this->roleClass, $this->roleRepositoryInterface);
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('oo_workflow_role_choice', $this->form->getName());
    }

    /**
     * Test Parent
     */
    public function testParent()
    {
        $this->assertSame('document', $this->form->getParent());
    }

    /**
     * Test the default options
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'class' => $this->roleClass,
            'property' => 'name',
            'choices' => $this->roles
        ));
    }
}

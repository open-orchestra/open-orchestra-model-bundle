<?php

namespace OpenOrchestra\ModelBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use OpenOrchestra\ModelInterface\Form\Type\AbstractWorkflowRoleChoiceType;

/**
 * class WorkflowRoleChoiceType
 */
class WorkflowRoleChoiceType extends AbstractWorkflowRoleChoiceType
{
    protected $roleClass;
    protected $roleRepositoryInterface;

    /**
     * @param string                  $roleClass            
     * @param RoleRepositoryInterface $roleRepositoryInterface            
     */
    public function __construct($roleClass, RoleRepositoryInterface $roleRepositoryInterface)
    {
        $this->roleClass = $roleClass;
        $this->roleRepositoryInterface = $roleRepositoryInterface;
    }

    /**
     *
     * @param OptionsResolver $resolver            
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'class' => $this->roleClass,
            'property' => 'name',
            'choices' => $this->getChoices()
        ));
    }

    /**
     * Returns roles list for workflow.
     */
    protected function getChoices()
    {
        return $this->roleRepositoryInterface->findWorkflowRole();
    }

    /**
     *
     * @return string
     */
    public function getParent()
    {
        return 'document';
    }
}


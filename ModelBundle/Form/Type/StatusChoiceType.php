<?php

namespace OpenOrchestra\ModelBundle\Form\Type;

use OpenOrchestra\ModelBundle\Form\DataTransformer\EmbedStatusToStatusTransformer;
use OpenOrchestra\ModelInterface\Form\Type\AbstractStatusChoiceType;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StatusChoiceType
 */
class StatusChoiceType extends AbstractStatusChoiceType
{
    protected $multiLanguagesChoiceManager;
    protected $statusTransformer;
    protected $statusClass;

    /**
     * @param EmbedStatusToStatusTransformer       $statusTransformer
     * @param string                               $statusClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     */
    public function __construct(EmbedStatusToStatusTransformer $statusTransformer, $statusClass, MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager)
    {
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->statusTransformer = $statusTransformer;
        $this->statusClass = $statusClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['embedded']) {
            $builder->addModelTransformer($this->statusTransformer);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $multiLanguagesChoiceManager = $this->multiLanguagesChoiceManager;
        $resolver->setDefaults(array(
            'embedded' => true,
            'class' => $this->statusClass,
            'choice_label' => function (StatusInterface $choice) use ($multiLanguagesChoiceManager) {
                return $multiLanguagesChoiceManager->choose($choice->getLabels());
            },
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'document';
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Form\Type;

use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelBundle\Form\DataTransformer\EmbedStatusToStatusTransformer;
use OpenOrchestra\ModelInterface\Form\Type\AbstractOrchestraStatusType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OrchestraStatusType
 */
class OrchestraStatusType extends AbstractOrchestraStatusType
{
    protected $translationChoiceManager;
    protected $statusTransformer;
    protected $statusClass;

    /**
     * @param EmbedStatusToStatusTransformer $statusTransformer
     * @param string                         $statusClass
     */
    public function __construct(EmbedStatusToStatusTransformer $statusTransformer, $statusClass, TranslationChoiceManager $translationChoiceManager)
    {
        $this->translationChoiceManager = $translationChoiceManager;
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
        $translationChoiceManager = $this->translationChoiceManager;
        $resolver->setDefaults(array(
            'embedded' => true,
            'class' => $this->statusClass,
            'choice_label' => function ($choice) use ($translationChoiceManager) {
                return $translationChoiceManager->choose($choice->getLabels());
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

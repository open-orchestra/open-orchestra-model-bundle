<?php

namespace OpenOrchestra\ModelBundle\Form\Type;

use OpenOrchestra\ModelBundle\Form\DataTransformer\EmbedStatusToStatusTransformer;
use OpenOrchestra\ModelInterface\Form\Type\AbstractStatusChoiceType;
use OpenOrchestra\ModelInterface\Manager\TranslationChoiceManagerInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StatusChoiceType
 */
class StatusChoiceType extends AbstractStatusChoiceType
{
    protected $translationChoiceManager;
    protected $statusTransformer;
    protected $statusClass;

    /**
     * @param EmbedStatusToStatusTransformer         $statusTransformer
     * @param string                                 $statusClass
     * @param TranslationChoiceManagerInterface|null $translationChoiceManager
     */
    public function __construct(EmbedStatusToStatusTransformer $statusTransformer, $statusClass, TranslationChoiceManagerInterface $translationChoiceManager = null)
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
            'choice_label' => function (StatusInterface $choice) use ($translationChoiceManager) {
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

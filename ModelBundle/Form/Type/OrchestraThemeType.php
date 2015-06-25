<?php

namespace OpenOrchestra\ModelBundle\Form\Type;

use OpenOrchestra\ModelInterface\Form\Type\AbstractOrchestraThemeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OrchestraThemeType
 */
class OrchestraThemeType extends AbstractOrchestraThemeType
{
    protected $themeClass;

    /**
     * @param string $themeClass
     */
    public function __construct($themeClass)
    {
        $this->themeClass = $themeClass;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => $this->themeClass,
                'property' => 'name',
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'document';
    }
}

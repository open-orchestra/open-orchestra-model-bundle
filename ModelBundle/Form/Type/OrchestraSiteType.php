<?php

namespace OpenOrchestra\ModelBundle\Form\Type;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\ModelBundle\Form\DataTransformer\EmbedSiteToSiteTransformer;
use OpenOrchestra\ModelInterface\Form\Type\AbstractOrchestraSiteType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OrchestraSiteType
 */
class OrchestraSiteType extends AbstractOrchestraSiteType
{
    protected $siteClass;
    protected $embedSiteToSiteTransformer;

    /**
     * @param string                     $siteClass
     * @param EmbedSiteToSiteTransformer $embedSiteToSiteTransformer
     */
    public function __construct($siteClass, EmbedSiteToSiteTransformer $embedSiteToSiteTransformer)
    {
        $this->siteClass = $siteClass;
        $this->embedSiteToSiteTransformer = $embedSiteToSiteTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['embed']) {
            $builder->addModelTransformer($this->embedSiteToSiteTransformer);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'embed' => false,
                'class' => $this->siteClass,
                'property' => 'name',
                'query_builder' => function (DocumentRepository $dr) {
                    return $dr->createQueryBuilder()->field('deleted')->equals(false);
                }
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

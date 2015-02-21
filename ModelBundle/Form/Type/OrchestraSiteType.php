<?php

namespace OpenOrchestra\ModelBundle\Form\Type;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\ModelInterface\Form\Type\AbstractOrchestraSiteType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraSiteType
 */
class OrchestraSiteType extends AbstractOrchestraSiteType
{
    protected $siteClass;

    /**
     * @param string $siteClass
     */
    public function __construct($siteClass)
    {
        $this->siteClass = $siteClass;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => $this->siteClass,
                'property' => 'name',
                'query_builder' => function (DocumentRepository $dr) {
                    return $dr->createQueryBuilder('s')->field('deleted')->equals(false);
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

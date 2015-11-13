<?php

namespace OpenOrchestra\ModelBundle\Tests\Form\Type;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Phake;
use OpenOrchestra\ModelBundle\Form\Type\GroupSiteChoiceType;

/**
 * Class GroupSiteChoiceTypeTest
 */
class GroupSiteChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GroupSiteChoiceType
     */
    protected $form;

    protected $transformer;
    protected $siteClass = 'SiteClass';

    /**
     * Set up the text
     */
    public function setUp()
    {
        $this->transformer = Phake::mock('OpenOrchestra\ModelBundle\Form\DataTransformer\EmbedSiteToSiteTransformer');

        $this->form = new GroupSiteChoiceType($this->siteClass, $this->transformer);
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('oo_group_site_choice', $this->form->getName());
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
            'class' => $this->siteClass,
            'property' => 'name',
            'query_builder' => function (DocumentRepository $dr) {
                return $dr->createQueryBuilder()->field('deleted')->equals(false);
            },
            'embed' => false,
        ));
    }

    /**
     * @param bool $embed
     * @param int  $transfomerTime
     *
     * @dataProvider provideEmbedAndTransformationTime
     */
    public function testBuildForm($embed, $transfomerTime)
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilderInterface');

        $this->form->buildForm($builder, array('embed' => $embed));

        Phake::verify($builder, Phake::times($transfomerTime))->addModelTransformer($this->transformer);
    }

    /**
     * @return array
     */
    public function provideEmbedAndTransformationTime()
    {
        return array(
            array(true, 1),
            array(false, 0),
        );
    }
}

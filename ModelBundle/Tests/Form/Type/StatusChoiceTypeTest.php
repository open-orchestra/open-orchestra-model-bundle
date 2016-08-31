<?php

namespace OpenOrchestra\ModelBundle\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ModelBundle\Form\Type\StatusChoiceType;

/**
 * Class StatusChoiceTypeTest
 */
class StatusChoiceTypeTest extends AbstractBaseTestCase
{
    /**
     * @var StatusChoiceType
     */
    protected $form;

    protected $builder;
    protected $transformer;
    protected $multiLanguagesManager;
    protected $statusClass = 'statusClass';

    /**
     * Set up the text
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->transformer = Phake::mock('OpenOrchestra\ModelBundle\Form\DataTransformer\EmbedStatusToStatusTransformer');
        $this->multiLanguagesManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface');

        $this->form = new StatusChoiceType($this->transformer, $this->statusClass, $this->multiLanguagesManager);
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('oo_status_choice', $this->form->getName());
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
        $multiLanguagesManager = $this->multiLanguagesManager;
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'embedded' => true,
            'class' => $this->statusClass,
            'choice_label' => function ($choice) use ($multiLanguagesManager) {
                return $multiLanguagesManager->choose($choice->getLabels());
            },
        ));
    }

    /**
     * @param array $config
     * @param int   $times
     *
     * @dataProvider provideConfigAndCount
     */
    public function testBuildForm($config, $times)
    {
        $this->form->buildForm($this->builder, $config);

        Phake::verify($this->builder, Phake::times($times))->addModelTransformer($this->transformer);
    }

    /**
     * @return array
     */
    public function provideConfigAndCount()
    {
        return array(
            array(array('embedded' => true), 1),
            array(array('embedded' => false), 0),
        );
    }
}

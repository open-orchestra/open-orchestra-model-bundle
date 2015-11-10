<?php

namespace OpenOrchestra\ModelBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\ModelBundle\Form\Type\OrchestraStatusType;

/**
 * Class OrchestraStatusTypeTest
 */
class OrchestraStatusTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraStatusType
     */
    protected $form;

    protected $builder;
    protected $transformer;
    protected $translationChoiceManager;
    protected $statusClass = 'statusClass';

    /**
     * Set up the text
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->transformer = Phake::mock('OpenOrchestra\ModelBundle\Form\DataTransformer\EmbedStatusToStatusTransformer');
        $this->translationChoiceManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\TranslationChoiceManagerInterface');

        $this->form = new OrchestraStatusType($this->transformer, $this->statusClass, $this->translationChoiceManager);
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('oo_orchestra_status', $this->form->getName());
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
        $translationChoiceManager = $this->translationChoiceManager;
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'embedded' => true,
            'class' => $this->statusClass,
            'choice_label' => function ($choice) use ($translationChoiceManager) {
                return $translationChoiceManager->choose($choice->getLabels());
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

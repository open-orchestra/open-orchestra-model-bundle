<?php

namespace OpenOrchestra\ModelBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\ModelBundle\Form\Type\SiteThemeChoiceType;

/**
 * Class SiteThemeChoiceTypeTest
 */
class SiteThemeChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ThemeChoiceType
     */
    protected $form;

    protected $builder;
    protected $themeClass = 'themeClass';

    /**
     * Set up the text
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');

        $this->form = new SiteThemeChoiceType($this->themeClass);
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('oo_site_theme_choice', $this->form->getName());
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
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'class' => $this->themeClass,
            'property' => 'name',
        ));
    }
}

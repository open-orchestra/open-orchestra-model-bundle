<?php

namespace OpenOrchestra\ModelBundle\Tests\DataFixtures\Loader;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\DataFixtures\Loader\OrchestraContainerAwareLoader;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use Phake;

/**
 * Test OrchestraContainerAwareLoaderTest
 */
class OrchestraContainerAwareLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraContainerAwareLoader
     */
    protected $loader;

    protected $container;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        Phake::when($this->container)
            ->getParameter('open_orchestra_model.production_fixtures_interface')
            ->thenReturn(array(
                    'OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface',
                    'OpenOrchestra\ModelBundle\Tests\DataFixtures\Loader\FakeProductionInterface',
                ));

        $this->loader = new OrchestraContainerAwareLoader($this->container);
    }

    /**
     * Test extension
     */
    public function testExtentds()
    {
        $this->assertInstanceOf('Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader', $this->loader);
    }

    /**
     * @param bool   $transient
     * @param string $className
     *
     * @dataProvider provideTransientAndClassName
     */
    public function testIsTransient($transient, $className)
    {
        $this->assertSame($transient, $this->loader->isTransient($className));
    }

    /**
     * @return array
     */
    public function provideTransientAndClassName()
    {
        return array(
            array(true, 'stdClass'),
            array(true, 'Doctrine\Common\DataFixtures\FixtureInterface'),
            array(true, 'OpenOrchestra\ModelBundle\Tests\DataFixtures\Loader\OnlyFixture'),
            array(true, 'OpenOrchestra\ModelBundle\Tests\DataFixtures\Loader\OnlyProductionFixture'),
            array(true, 'OpenOrchestra\ModelBundle\Tests\DataFixtures\Loader\ProductionAndFixture'),
            array(true, 'OpenOrchestra\ModelBundle\Tests\DataFixtures\Loader\FakeAndProduction'),
            array(false, 'OpenOrchestra\ModelBundle\Tests\DataFixtures\Loader\FakeProductionAndFixture'),
        );
    }
}

interface FakeProductionInterface {}

class OnlyFixture implements FixtureInterface
{
    public function load(ObjectManager $manager){}
}

class OnlyProductionFixture implements OrchestraProductionFixturesInterface {}

class FakeAndProduction implements  OrchestraProductionFixturesInterface, FakeProductionInterface {}

class ProductionAndFixture implements OrchestraProductionFixturesInterface, FixtureInterface
{
    public function load(ObjectManager $manager){}
}

class FakeProductionAndFixture implements OrchestraProductionFixturesInterface, FixtureInterface, FakeProductionInterface
{
    public function load(ObjectManager $manager){}
}

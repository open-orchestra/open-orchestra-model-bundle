<?php

namespace OpenOrchestra\ModelBundle\Tests\DataFixtures\Loader;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelBundle\DataFixtures\Loader\OrchestraContainerAwareLoader;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use Phake;

/**
 * Test OrchestraContainerAwareLoaderTest
 */
class OrchestraContainerAwareLoaderTest extends AbstractBaseTestCase
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
        $command = 'production';

        $this->container = Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        Phake::when($this->container)
            ->getParameter('open_orchestra_model.fixtures_interface.' . $command)
            ->thenReturn(array(
                    'OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface',
                    'OpenOrchestra\ModelBundle\Tests\DataFixtures\Loader\FakeProductionInterface',
                ));
        Phake::when($this->container)
            ->hasParameter('open_orchestra_model.fixtures_interface.' . $command)
            ->thenReturn(true);

        $this->loader = new OrchestraContainerAwareLoader($this->container, $command);
    }

    /**
     * Test extension
     */
    public function testExtentds()
    {
        $this->assertInstanceOf('Doctrine\Common\DataFixtures\Loader', $this->loader);
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

<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\Loader;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OrchestraContainerAwareLoader
 */
class OrchestraContainerAwareLoader extends Loader
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    public function isTransient($className)
    {
        $parentCheck = parent::isTransient($className);

        if (true == $parentCheck) {
            return true;
        }

        $orchestraProductionInterfaces = $this->container->getParameter('open_orchestra_model.production_fixtures_interface');

        $interfaces = class_implements($className);
        foreach ($orchestraProductionInterfaces as $interface) {
            if (!in_array($interface, $interfaces)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addFixture(FixtureInterface $fixture)
    {
        if ($fixture instanceof ContainerAwareInterface) {
            $fixture->setContainer($this->container);
        }

        parent::addFixture($fixture);
    }
}

<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\Loader;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OrchestraContainerAwareLoader
 */
class OrchestraContainerAwareLoader extends ContainerAwareLoader
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $interfaceType;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     * @param string             $interfaceType
     */
    public function __construct(ContainerInterface $container, $interfaceType)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->interfaceType = $interfaceType;
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

        if ($this->container->hasParameter('open_orchestra_model.fixtures_interface.' . $this->interfaceType)) {
            $orchestraFixturesInterfaces = $this->container->getParameter('open_orchestra_model.fixtures_interface.' . $this->interfaceType);

            $interfaces = class_implements($className);
            foreach ($orchestraFixturesInterfaces as $interface) {
                if (!in_array($interface, $interfaces)) {
                    return true;
                }
            }
        } else {
            return true;
        }

        return false;
    }
}

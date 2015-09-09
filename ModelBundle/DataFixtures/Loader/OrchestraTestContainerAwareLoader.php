<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\Loader;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OrchestraContainerAwareLoader
 */
class OrchestraTestContainerAwareLoader extends ContainerAwareLoader
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
        parent::__construct($container);
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

        $orchestraFunctionalInterfaces = $this->container->getParameter('open_orchestra_model.functional_fixtures_interface');

        $interfaces = class_implements($className);
        foreach ($orchestraFunctionalInterfaces as $interface) {
            if (!in_array($interface, $interfaces)) {
                return true;
            }
        }

        return false;
    }
}

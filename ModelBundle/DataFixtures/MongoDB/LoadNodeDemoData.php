<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\AbstractDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\HomeDataGenerator;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class LoadNodeDemoData
 */
class LoadNodeDemoData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface, OrchestraProductionFixturesInterface, OrchestraFunctionalFixturesInterface
{
    protected $nodede;
    protected $nodeen;
    protected $nodefr;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $languages = array("de", "en", "fr");

        $homeNode = new HomeDataGenerator($this, $this->container, $manager, 1, 'status-draft');
        $this->addNode($manager, $homeNode, $languages);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 560;
    }

    /**
     * @param ObjectManager         $manager
     * @param AbstractDataGenerator $dataGenerator
     * @param array                 $languages
     */
    protected function addNode(
        ObjectManager $manager,
        AbstractDataGenerator $dataGenerator,
        array $languages = array("fr", "en")
    ){
        foreach ($languages as $language) {
            $node = $dataGenerator->generateNode($language);
            $manager->persist($node);
            $this->setReference("node-".$node->getNodeId().'-'.$node->getLanguage().'-'.$node->getVersion(), $node);
        }
        $manager->flush();
    }
}

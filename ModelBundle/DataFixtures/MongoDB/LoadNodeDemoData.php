<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\AbstractDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\HomeDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\TransverseDataGenerator;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadNodeDemoData
 */
class LoadNodeDemoData extends AbstractFixture implements OrderedFixtureInterface, OrchestraProductionFixturesInterface, OrchestraFunctionalFixturesInterface
{
    protected $nodede;
    protected $nodeen;
    protected $nodefr;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $references = array();
        $references["status-published"] = $this->getReference('status-published');
        $references["status-draft"] = $this->getReference('status-draft');
        if ($this->hasReference('logo-orchestra')) {
            $references["logo-orchestra"] = $this->getReference('logo-orchestra');
        }
        $languages = array("de", "en", "fr");

        $transverseGenerator = new TransverseDataGenerator($references);
        foreach ($languages as $language) {
            $this->node{$language} = $transverseGenerator->generateNode($language);
            $this->setReference("node-global-".$language, $this->node{$language});
            $manager->persist($this->node{$language});
        }
        $homeNode = new HomeDataGenerator($references, 1, 'status-draft');
        $this->setReference("home-node", $homeNode);
        $this->addNode($manager, $homeNode, $languages);
        $manager->flush();
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
            $this->setReference("node-".$language, $node);
            $manager->persist($node);
        }
    }
}

<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\AbstractDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\CommunityDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\ContactDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\HomeDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\LegalDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\NewsDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\TransverseDataGenerator;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\Error404DataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\Error503DataGenerator;

/**
 * Class LoadNodeData
 */
class LoadNodeDemoData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
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
            $manager->persist($this->node{$language});
        }

        $this->addNode($manager, new HomeDataGenerator($references), $languages);
        $this->addNode($manager, new HomeDataGenerator($references, 2, 'status-draft'), array('fr'));
        $this->addNode($manager, new ContactDataGenerator($references), $languages);
        $this->addNode($manager, new LegalDataGenerator($references), $languages);
        $this->addNode($manager, new CommunityDataGenerator($references), $languages);
        $this->addNode($manager, new NewsDataGenerator($references), $languages);
        $this->addNode($manager, new Error404DataGenerator($references), $languages);
        $this->addNode($manager, new Error503DataGenerator($references), $languages);

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

    protected function addNode(
        ObjectManager $manager,
        AbstractDataGenerator $dataGenerator,
        array $languages = array("fr", "en")
    ){
        foreach ($languages as $language) {
            $node = $dataGenerator->generateNode($language);
            $this->addAreaRef($this->node{$language}, $node);
            $manager->persist($node);
        }
    }

    /**
     * @param NodeInterface $nodeTransverse
     * @param NodeInterface $node
     */
    protected function addAreaRef(NodeInterface $nodeTransverse, NodeInterface $node)
    {
        foreach ($node->getAreas() as $area) {
            foreach ($area->getBlocks() as $areaBlock) {
                if ($nodeTransverse->getNodeId() === $areaBlock['nodeId']) {
                    $block = $nodeTransverse->getBlock($areaBlock['blockId']);
                    $block->addArea(array('nodeId' => $node->getId(), 'areaId' => $area->getAreaId()));
                    $nodeTransverse->setBlock($areaBlock['blockId'], $block);
                }
            }
        }
    }
}

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

/**
 * Class LoadNodeData
 */
class LoadNodeDemoData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $manager->getConnection()->initialize();
        $dataBase = $manager->getDocumentDatabase('OpenOrchestra\ModelBundle\Document\Node');
        $dataBase->execute("
db.node.createIndex( { nodeId: 1, siteId: 1, language: 1, version: 1 }, { unique: true } )
db.system.js.save(
    {
        _id : 'duplicateNode' ,
        value : function (data){
            var node = null
            while (1) {
                var cursor = db.node.find( { nodeId: data.nodeId, siteId: data.siteId, language: data.language }).sort( { version: -1 } ).limit(1);
                node = cursor.next()
                delete node._id;
                node.version = node.version + 1;
                node.status = data.status;
                var results = db.node.insert(node);
                if( results.hasWriteError() ) {
                    if( results.getWriteError().code == 11000)
                        continue;
                    else
                        print( 'unexpected error inserting data: ' + tojson( results ) );
                }
                break;
            }
            return node;
        }
    }
);
");

        $references = array();
        $references["status-published"] = $this->getReference('status-published');
        $references["status-draft"] = $this->getReference('status-draft');

        $languages = array("en", "fr");

        $transverseGenerator = new TransverseDataGenerator($references);
        foreach ($languages as $language) {
            $node = $transverseGenerator->generateNode($language);
            $manager->persist($node);
        }

        $this->addNode($manager, new HomeDataGenerator($references), $transverseGenerator, $languages);
        $this->addNode($manager, new HomeDataGenerator($references, 2, 'status-draft'), $transverseGenerator, array('fr'));
        $this->addNode($manager, new ContactDataGenerator($references), $transverseGenerator, $languages);
        $this->addNode($manager, new LegalDataGenerator($references), $transverseGenerator, $languages);
        $this->addNode($manager, new CommunityDataGenerator($references), $transverseGenerator, $languages);
        $this->addNode($manager, new NewsDataGenerator($references), $transverseGenerator, $languages);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 61;
    }

    protected function addNode(ObjectManager $manager, AbstractDataGenerator $dataGenerator, TransverseDataGenerator $transverseGenerator, array $languages = array("fr", "en"))
    {
        foreach ($languages as $language) {
            $node = $dataGenerator->generateNode($language);
            $this->addAreaRef($transverseGenerator->generateNode($language), $node);
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
                }
            }
        }
    }
}

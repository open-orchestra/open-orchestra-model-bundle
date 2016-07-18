<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\AbstractDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\CommunityDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\ContactDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\NodeRootFunctionalDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\LegalDataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\NewsDataGenerator;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\Error404DataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\Error503DataGenerator;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\FooterStrategy;

/**
 * Class LoadNodeRootFunctionalDemoData
 */
class LoadNodeRootFunctionalDemoData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
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
        
        foreach ($languages as $language) {
            $references["node-".$language] = $this->getReference("node-".$language);
            $references["node-global-".$language] = $this->getReference("node-global-".$language);
            $this->node{$language} = $references["node-global-".$language];
            $this->generateNodeGlobal($language);
        }
        $this->addNode($manager, new NodeRootFunctionalDataGenerator($references), $languages);
        $this->addNode($manager, new NodeRootFunctionalDataGenerator($references, 2, 'status-draft'), array('fr'));
        $this->addNode($manager, new LegalDataGenerator($references), $languages);
        $this->addNode($manager, new ContactDataGenerator($references), $languages);
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
        return 570;
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
    ) {
        foreach ($languages as $language) {
            $node = $dataGenerator->generateNode($language);
            $manager->persist($node);
            $this->addAreaRef($this->node{$language}, $node);
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

    /**
     * @param string $language
     *
     * @return Node
     */
    protected function generateNodeGlobal($language)
    {
        $siteBlockLogo = new Block();
        $siteBlockLogo->setLabel('Wysiwyg logo');
        $siteBlockLogo->setClass('logo');
        $siteBlockLogo->setComponent(TinyMCEWysiwygStrategy::NAME);
        $orchestraTitle = "Open Orchestra";
        if (isset($this->references['logo-orchestra'])) {
            $orchestraTitle = '[media=original]' . $this->references['logo-orchestra']->getId() . '[/media]';
        }
        $siteBlockLogo->setAttributes(
            array(
                "htmlContent" =>
                    '<a href="/" id="myLogo">' . $orchestraTitle . '</a>',
            )
        );
        $siteBlockLogo->addArea(array('nodeId' => 0, 'areaId' => 'main'));
        $siteBlockMainMenu = new Block();
        $siteBlockMainMenu->setLabel('Menu');
        $siteBlockMainMenu->setComponent('menu');
        $siteBlockMainMenu->setId('myMainMenu');
        $siteBlockMainMenu->setClass('my-main-menu');
        $siteBlockMainMenu->addArea(array('nodeId' => 0, 'areaId' => 'main'));
        $siteBlockFooter = new Block();
        $siteBlockFooter->setLabel('Wysiwyg footer');
        $siteBlockFooter->setComponent(TinyMCEWysiwygStrategy::NAME);
        $siteBlockFooter->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='footer-networks'>
    <h4>Networks</h4>
    <ul>
        <li><a href="http://www.businessdecision.fr/">http://www.businessdecision.fr/</a></li>
        <li><a href="http://www.interakting.com/">http://www.interakting.com/</a></li>
    </ul>
</div>
<div class="footer-contact">
    <h4>Contact</h4>
    <ul>
        <li>Interakting</li>
        <li>153 Rue de Courcelles</li>
        <li>75017 Paris France</li>
        <li>01 56 21 21 21</li>
        <li><a href='/node/fixture_page_contact'>contact@interakting.com</a></li>
    </ul>
</div>
EOF
        ));
        $siteBlockFooter->addArea(array('nodeId' => 0, 'areaId' => 'main'));
        $siteBlockFooterMenu = new Block;
        $siteBlockFooterMenu->setLabel('footer menu');
        $siteBlockFooterMenu->setClass("footer-legal");
        $siteBlockFooterMenu->setComponent(FooterStrategy::NAME);
        $siteBlockFooterMenu->addArea(array('nodeId' => 0, 'areaId' => 'main'));
        $siteBlockContact = new Block();
        $siteBlockContact->setLabel('Contact');
        $siteBlockContact->setComponent('contact');
        $siteBlockContact->setId('myFormContact');
        $siteBlockContact->setClass('my-form-contact');
        $siteBlockContact->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $mainArea = $this->node{$language}->getArea()->getAreas();
        foreach ($mainArea as $area) {
            if ($area->getAreaId() == "main") {
                $area->addBlock(array('nodeId' => 0, 'blockId' => 0));
                $area->addBlock(array('nodeId' => 0, 'blockId' => 1));
                $area->addBlock(array('nodeId' => 0, 'blockId' => 2));
                $area->addBlock(array('nodeId' => 0, 'blockId' => 3));
            }
        }
        $this->node{$language}->addBlock($siteBlockLogo);
        $this->node{$language}->addBlock($siteBlockMainMenu);
        $this->node{$language}->addBlock($siteBlockFooter);
        $this->node{$language}->addBlock($siteBlockFooterMenu);
        $this->node{$language}->addBlock($siteBlockContact);

        return $this->node{$language};
    }
}

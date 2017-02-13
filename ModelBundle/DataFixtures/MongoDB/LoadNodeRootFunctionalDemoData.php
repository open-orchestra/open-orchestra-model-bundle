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
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\Error404DataGenerator;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\Error503DataGenerator;
use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\FooterStrategy;
use OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent\AutoPublishDataGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\LanguageListStrategy;

/**
 * Class LoadNodeRootFunctionalDemoData
 */
class LoadNodeRootFunctionalDemoData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
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
        foreach($languages as $language) {
            $this->generateGlobalBlock($manager, $language);
        }

        $this->addNode($manager, new NodeRootFunctionalDataGenerator($this, $this->container, $manager), $languages);
        $this->addNode($manager, new NodeRootFunctionalDataGenerator($this, $this->container, $manager, 2, 'status-draft'), array('fr'));
        $this->addNode($manager, new LegalDataGenerator($this, $this->container, $manager), $languages);
        $this->addNode($manager, new ContactDataGenerator($this, $this->container, $manager), $languages);
        $this->addNode($manager, new CommunityDataGenerator($this, $this->container, $manager), $languages);
        $this->addNode($manager, new NewsDataGenerator($this, $this->container, $manager), $languages);
        $this->addNode($manager, new Error404DataGenerator($this, $this->container, $manager), $languages);
        $this->addNode($manager, new Error503DataGenerator($this, $this->container, $manager), $languages);
        $this->addNode($manager, new AutoPublishDataGenerator($this, $this->container, $manager), array('fr', 'en'));
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
            $this->setReference("node-".$node->getNodeId().'-'.$node->getLanguage().'-'.$node->getVersion(), $node);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager  $manager
     * @param BlockInterface $block
     * @param string         $language
     */
    protected function generateBlock(ObjectManager $manager, BlockInterface $block, $language)
    {
        $block->setSiteId('2');
        $block->setLanguage($language);

        $manager->persist($block);
        $manager->flush();

        $this->setReference($block->getLabel().'-'.$language, $block);
    }

    /**
     * @param ObjectManager $manager
     * @param string        $language
     */
    protected function generateGlobalBlock(ObjectManager $manager, $language)
    {
        $siteBlockLogo = new Block();
        $siteBlockLogo->setLabel('Wysiwyg logo');
        $siteBlockLogo->setStyle('default');
        $siteBlockLogo->setComponent(TinyMCEWysiwygStrategy::NAME);
        $siteBlockLogo->setTransverse(true);
        $orchestraTitle = "Open Orchestra";
        if ($this->hasReference('logo-orchestra')) {
            $orchestraTitle = '[media=original]' . $this->getReference('logo-orchestra')->getId() . '[/media]';
        }
        $siteBlockLogo->setAttributes(
            array(
                "htmlContent" =>
                    '<a href="/" id="myLogo">' . $orchestraTitle . '</a>',
            )
        );
        $this->generateBlock($manager, $siteBlockLogo, $language);

        $siteBlockMainMenu = new Block();
        $siteBlockMainMenu->setLabel('Menu');
        $siteBlockMainMenu->setComponent('menu');
        $siteBlockMainMenu->setStyle('default');
        $siteBlockMainMenu->setTransverse(true);
        $this->generateBlock($manager, $siteBlockMainMenu, $language);

        $siteBlockFooter = new Block();
        $siteBlockFooter->setLabel('Wysiwyg footer');
        $siteBlockFooter->setComponent(TinyMCEWysiwygStrategy::NAME);
        $siteBlockFooter->setTransverse(true);
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
        $this->generateBlock($manager, $siteBlockFooter, $language);

        $siteBlockFooterMenu = new Block;
        $siteBlockFooterMenu->setLabel('footer menu');
        $siteBlockFooterMenu->setStyle('default');
        $siteBlockFooterMenu->setComponent(FooterStrategy::NAME);
        $siteBlockFooterMenu->setTransverse(true);
        $this->generateBlock($manager, $siteBlockFooterMenu, $language);

        $siteBlockContact = new Block();
        $siteBlockContact->setLabel('Contact');
        $siteBlockContact->setComponent('contact');
        $siteBlockContact->setStyle('default');
        $this->generateBlock($manager, $siteBlockContact, $language);

        $siteBlockLanguage = new Block();
        $siteBlockLanguage->setLabel('Language list');
        $siteBlockLanguage->setComponent(LanguageListStrategy::NAME);
        $siteBlockLanguage->setTransverse(true);
        $this->generateBlock($manager, $siteBlockLanguage, $language);
    }
}

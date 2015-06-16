<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelBundle\Document\Area;
use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
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
        $transverseFr = $this->generateNodeTransverse('fr');
        $manager->persist($transverseFr);
        $manager->persist($this->generateNodeTransverse('en'));

        $siteHome = $this->generateNodeSiteHome($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteHome);
        $manager->persist($siteHome);

        $siteWhat = $this->generateNodeSiteWhatIsOrchestra($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteWhat);
        $manager->persist($siteWhat);

        $siteAboutUs = $this->generateNodeSiteAboutUs($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteAboutUs);
        $manager->persist($siteAboutUs);

        $siteCommunity = $this->generateNodeSiteCommunity($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteCommunity);
        $manager->persist($siteCommunity);

        $siteContact = $this->generateNodeSiteContact($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteContact);
        $manager->persist($siteContact);

        $siteDocumentation = $this->generateNodeSiteDocumentation($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteDocumentation);
        $manager->persist($siteDocumentation);

        $siteJoinUs = $this->generateNodeSiteJoinUs($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteJoinUs);
        $manager->persist($siteJoinUs);

        $siteLegalMention = $this->generateNodeSiteLegalMentions($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteLegalMention);
        $manager->persist($siteLegalMention);

        $siteNetwork = $this->generateNodeSiteNetwork($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteNetwork);
        $manager->persist($siteNetwork);

        $siteNews = $this->generateNodeSiteNews($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteNews);
        $manager->persist($siteNews);

        $siteOurTeam = $this->generateNodeSiteOurTeam($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteOurTeam);
        $manager->persist($siteOurTeam);

        $siteStart = $this->generateNodeSiteStartOrchestra($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteStart);
        $manager->persist($siteStart);

        $manager->persist($transverseFr);

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

    /**
     * @param string $language
     *
     * @return Node
     */
    public function generateNodeTransverse($language)
    {
        $siteHomeBlock0 = new Block();
        $siteHomeBlock0->setLabel('Wysiwyg 1');
        $siteHomeBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteHomeBlock0->setAttributes(array(
            "htmlContent" => '<a href="#" id="myLogo"> <img src="http://open-orchestra.com/media/open-orchestra-logo.png" /> </a>',
        ));
        $siteHomeBlock0->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $siteHomeBlockMenu = new Block();
        $siteHomeBlockMenu->setLabel('Menu');
        $siteHomeBlockMenu->setComponent('menu');
        $siteHomeBlockMenu->setId('myMainMenu');
        $siteHomeBlockMenu->setClass('my-main-menu');
        $siteHomeBlockMenu->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $siteHomeBlock4 = new Block();
        $siteHomeBlock4->setLabel('Wysiwyg 2');
        $siteHomeBlock4->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteHomeBlock4->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='footer-infos'>
    <h4>Infos</h4>
    <ul>
        <li><a href="/node/fixture_page_about_us">Qui sommes nous ?</a></li>
        <li><a href="/node/fixture_page_contact">Contact</a></li>
    </ul>
</div>
<div class="footer-legal">
    <h4>Légal</h4>
    <ul>
        <li><a href="#">Mentions Légal</a></li>
        <li><a href="/node/fixture_page_networks">Plan du site</a></li>
    </ul>
</div>
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
        $siteHomeBlock4->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $siteWhatBlock5 = new Block();
        $siteWhatBlock5->setLabel('What block');
        $siteWhatBlock5->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteWhatBlock5->setAttributes(array(
            "htmlContent" => <<<EOF
<div class="news">
    <h3 class="bloc-title">
        <p class="title-module">Actu</p>
    </h3>
    <div class="block-content">
        NEWS
    </div>
</div>
EOF
        ));
        $siteWhatBlock5->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $siteWhatBlock6 = new Block();
        $siteWhatBlock6->setLabel('Contact');
        $siteWhatBlock6->setComponent('contact');
        $siteWhatBlock6->setId('myFormContact');
        $siteWhatBlock6->setClass('my-form-contact');
        $siteWhatBlock6->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $mainArea = new Area();
        $mainArea->setLabel('main');
        $mainArea->setAreaId('main');
        $mainArea->setHtmlClass("main");
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 1));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 2));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 3));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 4));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 5));

        $nodeTransverse = new Node();
        $nodeTransverse->setNodeId(NodeInterface::TRANSVERSE_NODE_ID);
        $nodeTransverse->setMaxAge(1000);
        $nodeTransverse->setNodeType(NodeInterface::TYPE_TRANSVERSE);
        $nodeTransverse->setName(NodeInterface::TRANSVERSE_NODE_ID);
        $nodeTransverse->setSiteId('2');
        $nodeTransverse->setParentId('-');
        $nodeTransverse->setPath('-');
        $nodeTransverse->setVersion(1);
        $nodeTransverse->setLanguage($language);
        $nodeTransverse->setStatus($this->getReference('status-draft'));
        $nodeTransverse->setDeleted(false);
        $nodeTransverse->setTemplateId('');
        $nodeTransverse->setTheme('');
        $nodeTransverse->setInFooter(false);
        $nodeTransverse->setInMenu(false);
        $nodeTransverse->addArea($mainArea);
        $nodeTransverse->addBlock($siteHomeBlock0);
        $nodeTransverse->addBlock($siteHomeBlockMenu);
        $nodeTransverse->addBlock($siteHomeBlock4);
        $nodeTransverse->addBlock($siteWhatBlock5);
        $nodeTransverse->addBlock($siteWhatBlock6);

        return $nodeTransverse;
    }

    /**
     * @return Node
     */
    public function generateNodeSiteHome()
    {
        $siteHomeBlock1 = new Block();
        $siteHomeBlock1->setLabel('Wysiwyg 1');
        $siteHomeBlock1->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteHomeBlock1->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='content'>
    <p>
        Business & Decision est un Groupe international de services numériques,  leader de la Business Intelligence (BI) et du CRM, acteur majeur de l'e-Business.
        Le Groupe contribue à la réussite des projets à forte valeur ajoutée des entreprises et accompagne ses clients dans des domaines d’innovation tels que le Big Data et le Digital.
        Il est reconnu pour son expertise fonctionnelle et technologique par les plus grands éditeurs de logiciels du marché avec lesquels il a noué des partenariats. Fort d’une expertise unique dans ses domaines de spécialisation, Business & Decision offre des solutions adaptées à des secteurs d’activité ainsi qu’à des directions métiers.
    </p>
</div>
EOF
        ));
        $siteHomeBlock1->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea2'));

        $siteHomeArea1 = new Area();
        $siteHomeArea1->setLabel('Logo');
        $siteHomeArea1->setAreaId('logo');
        $siteHomeArea1->setHtmlClass('logo');
        $siteHomeArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $siteHomeArea2 = new Area();
        $siteHomeArea2->setLabel('Main menu');
        $siteHomeArea2->setAreaId('mainMenu');
        $siteHomeArea2->setHtmlClass('main-menu');
        $siteHomeArea2->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteHomeArea0 = new Area();
        $siteHomeArea0->setLabel('Header');
        $siteHomeArea0->setAreaId('header');
        $siteHomeArea0->setHtmlClass('header');
        $siteHomeArea0->addArea($siteHomeArea1);
        $siteHomeArea0->addArea($siteHomeArea2);

        $siteHomeArea4 = new Area();
        $siteHomeArea4->setLabel('Main content area 2');
        $siteHomeArea4->setAreaId('mainContentArea2');
        $siteHomeArea4->setHtmlClass('main-content-area2');
        $siteHomeArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $siteHomeArea3 = new Area();
        $siteHomeArea3->setLabel('My main');
        $siteHomeArea3->setAreaId('myMain');
        $siteHomeArea3->addArea($siteHomeArea4);

        $siteHomeFooter = new Area();
        $siteHomeFooter->setLabel('Containe footer');
        $siteHomeFooter->setAreaId('containeFooter');
        $siteHomeFooter->setHtmlClass('containe-footer');
        $siteHomeFooter->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));

        $siteHomeContainerFooter = new Area();
        $siteHomeContainerFooter->setLabel('Footer');
        $siteHomeContainerFooter->setAreaId('footer');
        $siteHomeContainerFooter->setHtmlClass('footer');
        $siteHomeContainerFooter->addArea($siteHomeFooter);

        $siteHome = new Node();
        $siteHome->setNodeId(NodeInterface::ROOT_NODE_ID);
        $siteHome->setMaxAge(1000);
        $siteHome->setNodeType('page');
        $siteHome->setName('Home');
        $siteHome->setSiteId('2');
        $siteHome->setParentId('-');
        $siteHome->setPath('-');
        $siteHome->setRoutePattern('/');
        $siteHome->setVersion(1);
        $siteHome->setLanguage('fr');
        $siteHome->setStatus($this->getReference('status-published'));
        $siteHome->setDeleted(false);
        $siteHome->setTemplateId('');
        $siteHome->setTheme('themePresentation');
        $siteHome->setInFooter(false);
        $siteHome->setInMenu(true);
        $siteHome->addArea($siteHomeArea0);
        $siteHome->addArea($siteHomeArea3);
        $siteHome->addArea($siteHomeContainerFooter);
        $siteHome->addBlock($siteHomeBlock1);

        $siteHome->setSitemapChangefreq('always');
        $siteHome->setSitemapPriority('0.75');

        return $siteHome;
    }

    /**
     * @return Node
     */
    public function generateNodeSiteWhatIsOrchestra()
    {
        $siteWhatBlock0 = new Block();
        $siteWhatBlock0->setLabel('Wysiwyg');
        $siteWhatBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteWhatBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='content2'>
    <h1>OpenOrchestra</h1>
    <p>
        PHP Orchestra est une plateforme développée conjointement par Interakting et Zend Technologies. Cette offre, dédiée au marketing est destinée aux grands projets de nouvelle génération en digital marketing et entreprise 2.0.
        L’objectif de PHP Factory est de répondre aux exigences les plus élevées des grands comptes en termes de haute disponibilité, de performance et d'industrialisation des processus de création et de diffusion de contenus vers le Web et les mobiles.
    </p>
    <p>
        Elle a été développé  autour des standards PHP de Zend. Elle est constituée d’une bibliothèque de composants : gestion de contenu web et multi-média, d’e-commerce, d’animation de réseaux sociaux, de Portail et de Mobilité.
        PHP FACTORY est la solution omnicanal qui accélère la construction de votre écosysteme digital.Quelles que soient les interactions entre une marque et ses clients, quel que soit l’écran, quel que soit le terminal, l’expérience se construit à chaque point de contact.
    </p>
    <p>
        Notre promesse : « Economies d’échelle et mutualisation des investissements pour une expérience web cohérente sur tous les canaux fixe mobile, tablette, TV, bornes… »
    <p>
    <p>
        Une solution ciblée :
        <ul>
            <li>Projet où «l’expérience», qu’elle soit clients, collaborateurs, partenaires ou distributeurs est au cœur de la problématique.</li>
            <li>Projet à dimension internationale nécessitant des économies d’échelles.</li>
            <li>Projet avec des équations complexes à résoudre et où les systèmes d’informations internes et partenaires sont fortement sollicités.</li>
            <li>Projet dont l’objectif est de bâtir des écosystèmes digitaux (e-commerce, communication, référentiel, selfcare, mobilité, distribution, …) cohérents avec des synergies fonctionnelles et technologiques.</li>
            <li>Nativement, multi-sites multi support, facile d’intégration au SI, ouvert vers l’extérieur, taillé pour les fortes charges et la sécurité, modulaire (tout est composant, modèle HMVC), 100% Zend.</li>
        </ul>
    </p>
</div>
EOF
        ));
        $siteWhatBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteWhatArea1 = new Area();
        $siteWhatArea1->setLabel('Logo');
        $siteWhatArea1->setAreaId('logo');
        $siteWhatArea1->setHtmlClass('logo');
        $siteWhatArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $siteWhatArea2 = new Area();
        $siteWhatArea2->setLabel('Main menu');
        $siteWhatArea2->setAreaId('mainMenu');
        $siteWhatArea2->setHtmlClass('main-menu');
        $siteWhatArea2->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteWhatArea0 = new Area();
        $siteWhatArea0->setLabel('Header');
        $siteWhatArea0->setAreaId('header');
        $siteWhatArea0->setHtmlClass('header');
        $siteWhatArea0->addArea($siteWhatArea1);
        $siteWhatArea0->addArea($siteWhatArea2);

        $siteWhatArea4 = new Area();
        $siteWhatArea4->setLabel('Main content area 1');
        $siteWhatArea4->setAreaId('mainContentArea1');
        $siteWhatArea4->setHtmlClass('main-content-area1');
        $siteWhatArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $siteWhatArea5 = new Area();
        $siteWhatArea5->setLabel('Module area');
        $siteWhatArea5->setAreaId('moduleArea');
        $siteWhatArea5->setHtmlClass('module-area');
        $siteWhatArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));
        $siteWhatArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));

        $siteWhatArea3 = new Area();
        $siteWhatArea3->setLabel('My main');
        $siteWhatArea3->setAreaId('myMain');
        $siteWhatArea3->setHtmlClass('my-main');
        $siteWhatArea3->addArea($siteWhatArea4);
        $siteWhatArea3->addArea($siteWhatArea5);

        $siteWhatArea7 = new Area();
        $siteWhatArea7->setLabel('Containe footer');
        $siteWhatArea7->setAreaId('containeFooter');
        $siteWhatArea7->setHtmlClass('containe-footer');
        $siteWhatArea7->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteWhatArea6 = new Area();
        $siteWhatArea6->setLabel('Footer');
        $siteWhatArea6->setAreaId('footer');
        $siteWhatArea6->setHtmlClass("footer");
        $siteWhatArea6->addArea($siteWhatArea7);

        $siteWhat = new Node();
        $siteWhat->setNodeId('fixture_page_what_is_orchestra');
        $siteWhat->setMaxAge(1000);
        $siteWhat->setNodeType('page');
        $siteWhat->setName('Orchestra ?');
        $siteWhat->setSiteId('2');
        $siteWhat->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteWhat->setPath('-');
        $siteWhat->setOrder(0);
        $siteWhat->setRoutePattern('/page-what-is-orchestra');
        $siteWhat->setVersion(1);
        $siteWhat->setLanguage('fr');
        $siteWhat->setStatus($this->getReference('status-published'));
        $siteWhat->setDeleted(false);
        $siteWhat->setTemplateId('');
        $siteWhat->setTheme('themePresentation');
        $siteWhat->setInFooter(false);
        $siteWhat->setInMenu(true);
        $siteWhat->addArea($siteWhatArea0);
        $siteWhat->addArea($siteWhatArea3);
        $siteWhat->addArea($siteWhatArea6);
        $siteWhat->addBlock($siteWhatBlock0);

        $siteWhat->setSitemapChangefreq('hourly');
        $siteWhat->setSitemapPriority('0.8');

        return $siteWhat;
    }

    /**
     * @return Node
     */
    public function generateNodeSiteStartOrchestra()
    {
        $siteStartBlock0 = new Block();
        $siteStartBlock0->setLabel('Wysiwyg');
        $siteStartBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteStartBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='content2'>
    <h1>Le tour rapide</h1>
    <p>
        Quoi de mieux pour se faire un avis que d'essayer Symfony par vous même ?
        À part un peu de temps, cela ne vous coûtera rien. Pas à pas vous allez explorer l'univers de Symfony.
        Attention, Symfony peut vite devenir addictif dès la première rencontre.
    </p>
</div>
EOF
));
        $siteStartBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteStartArea1 = new Area();
        $siteStartArea1->setLabel('Logo');
        $siteStartArea1->setAreaId('logo');
        $siteStartArea1->setHtmlClass('logo');
        $siteStartArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $siteStartArea2 = new Area();
        $siteStartArea2->setLabel('Main menu');
        $siteStartArea2->setAreaId('mainMenu');
        $siteStartArea2->setHtmlClass('main-menu');
        $siteStartArea2->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteStartArea0 = new Area();
        $siteStartArea0->setLabel('Header');
        $siteStartArea0->setAreaId('header');
        $siteStartArea0->setHtmlClass('header');
        $siteStartArea0->addArea($siteStartArea1);
        $siteStartArea0->addArea($siteStartArea2);

        $siteStartArea4 = new Area();
        $siteStartArea4->setLabel('Main content area 1');
        $siteStartArea4->setAreaId('mainContentArea1');
        $siteStartArea4->setHtmlClass('main-content-area1');
        $siteStartArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $siteStartArea5 = new Area();
        $siteStartArea5->setLabel('Module area');
        $siteStartArea5->setAreaId('moduleArea');
        $siteStartArea5->setHtmlClass('module-area');
        $siteStartArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));
        $siteStartArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));

        $siteStartArea3 = new Area();
        $siteStartArea3->setLabel('My main');
        $siteStartArea3->setAreaId('myMain');
        $siteStartArea3->setHtmlClass('my-main');
        $siteStartArea3->addArea($siteStartArea4);
        $siteStartArea3->addArea($siteStartArea5);

        $siteStartArea7 = new Area();
        $siteStartArea7->setLabel('Containe footer');
        $siteStartArea7->setAreaId('containeFooter');
        $siteStartArea7->setHtmlClass('containe-footer');
        $siteStartArea7->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));

        $siteStartArea6 = new Area();
        $siteStartArea6->setLabel('Footer');
        $siteStartArea6->setAreaId('footer');
        $siteStartArea6->setHtmlClass('footer');
        $siteStartArea6->addArea($siteStartArea7);

        $siteStart = new Node();
        $siteStart->setNodeId('fixture_page_start_orchestra');
        $siteStart->setMaxAge(1000);
        $siteStart->setNodeType('page');
        $siteStart->setName('Get Started');
        $siteStart->setSiteId('2');
        $siteStart->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteStart->setPath('-');
        $siteStart->setRoutePattern('/page-start-orchestra');
        $siteStart->setVersion(1);
        $siteStart->setOrder(1);
        $siteStart->setLanguage('fr');
        $siteStart->setStatus($this->getReference('status-published'));
        $siteStart->setDeleted(false);
        $siteStart->setTemplateId('');
        $siteStart->setTheme('themePresentation');
        $siteStart->setInFooter(false);
        $siteStart->setInMenu(true);
        $siteStart->addArea($siteStartArea0);
        $siteStart->addArea($siteStartArea3);
        $siteStart->addArea($siteStartArea6);
        $siteStart->addBlock($siteStartBlock0);

        $siteStart->setSitemapChangefreq('monthly');
        $siteStart->setSitemapPriority('0.25');


        return $siteStart;
    }

    /**
     * @return Node
     */
    public function generateNodeSiteDocumentation()
    {
        $siteDocBlock0 = new Block();
        $siteDocBlock0->setLabel('Wysiwyg');
        $siteDocBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteDocBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='content2'>
<h1>PHP Documentation</h1>
    <p>The PHP Manual is available online in a selection of languages. Please pick a language from the list below.

    More information about php.net URL shortcuts by visiting our URL how to page.

    Note, that many languages are just under translation, and the untranslated parts are still in English. Also some translated parts might be outdated. The translation teams are open to contributions.</p>
</div>
EOF
        ));
        $siteDocBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteDocArea1 = new Area();
        $siteDocArea1->setLabel('Logo');
        $siteDocArea1->setAreaId('logo');
        $siteDocArea1->setHtmlClass('logo');
        $siteDocArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $siteDocArea2 = new Area();
        $siteDocArea2->setLabel('Main menu');
        $siteDocArea2->setAreaId('mainMenu');
        $siteDocArea2->setHtmlClass('main-menu');
        $siteDocArea2->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteDocArea0 = new Area();
        $siteDocArea0->setLabel('Header');
        $siteDocArea0->setAreaId('header');
        $siteDocArea0->setHtmlClass('header');
        $siteDocArea0->addArea($siteDocArea1);
        $siteDocArea0->addArea($siteDocArea2);

        $siteDocArea4 = new Area();
        $siteDocArea4->setLabel('Main content area 1');
        $siteDocArea4->setAreaId('mainContentArea1');
        $siteDocArea4->setHtmlClass('main-content-area1');
        $siteDocArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $siteDocArea5 = new Area();
        $siteDocArea5->setLabel('Module area');
        $siteDocArea5->setAreaId('moduleArea');
        $siteDocArea5->setHtmlClass('module-area');
        $siteDocArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));
        $siteDocArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));

        $siteDocArea3 = new Area();
        $siteDocArea3->setLabel('My main');
        $siteDocArea3->setAreaId('myMain');
        $siteDocArea3->setHtmlClass('my-main');
        $siteDocArea3->addArea($siteDocArea4);
        $siteDocArea3->addArea($siteDocArea5);

        $siteDocArea7 = new Area();
        $siteDocArea7->setLabel('Containe footer');
        $siteDocArea7->setAreaId('containeFooter');
        $siteDocArea7->setHtmlClass('containe-footer');
        $siteDocArea7->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));

        $siteDocArea6 = new Area();
        $siteDocArea6->setLabel('Footer');
        $siteDocArea6->setAreaId('footer');
        $siteDocArea6->setHtmlClass('footer');
        $siteDocArea6->addArea($siteDocArea7);

        $siteDoc = new Node();
        $siteDoc->setNodeId('fixture_page_documentation');
        $siteDoc->setMaxAge(1000);
        $siteDoc->setNodeType('page');
        $siteDoc->setName('Documentation');
        $siteDoc->setSiteId('2');
        $siteDoc->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteDoc->setOrder(2);
        $siteDoc->setPath('-');
        $siteDoc->setRoutePattern('/page-documentation');
        $siteDoc->setVersion(1);
        $siteDoc->setLanguage('fr');
        $siteDoc->setStatus($this->getReference('status-published'));
        $siteDoc->setDeleted(false);
        $siteDoc->setTemplateId('');
        $siteDoc->setTheme('themePresentation');
        $siteDoc->setInFooter(false);
        $siteDoc->setInMenu(true);
        $siteDoc->addArea($siteDocArea0);
        $siteDoc->addArea($siteDocArea3);
        $siteDoc->addArea($siteDocArea6);
        $siteDoc->addBlock($siteDocBlock0);

        return $siteDoc;
    }

    /**
     * @return Node
     */
    public function generateNodeSiteCommunity()
    {
        $siteComBlock0 = new Block();
        $siteComBlock0->setLabel('Wysiwyg 1');
        $siteComBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteComBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='content2'>
    <h1>ENTREPRISE DIGITALE : LES LEVIERS DE LA PERFORMANCE</h1>
    <p>
        L’Entreprise Digitale n’est plus un concept abstrait mais bien un formidable levier de la performance.

        Pour interakting, l’entreprise digitale regroupe toutes les initatives autour des relations collaborateurs, partenaires, distributeurs, l’amélioration des processus avec des applications métiers repensées et l’industrialisation de la communication institutionnelle. Agilité, rapidité, fluidité et interactions sont les maîtres mots.

        Pour les collaborateurs, l’accent est mis sur les réseaux sociaux d’entreprise, les plateformes collaboratives et le social business.
        Pour les partenaires et distributeurs, c’est le commerce B2B, les espaces clients (selfcare) de nouvelles générations, le feedback management et la cocréation qui sont au devant de la scène.
        Pour la communication institutionnelle, être capable de gérer une communication de crise, une empreinte digitale et sa réputation au niveau international est une préoccupation principale.

        Enfin, la digitalisation des applications et les stratégies mobiles de l’entreprise (équipement des vendeurs et des intermédiaires), les dispositifs relations B2B2C, sont aujourd’hui les nouveaux challenges.
    </p>
</div>
EOF
        ));
        $siteComBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteComArea1 = new Area();
        $siteComArea1->setLabel('Logo');
        $siteComArea1->setAreaId('logo');
        $siteComArea1->setHtmlClass('logo');
        $siteComArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $siteComArea2 = new Area();
        $siteComArea2->setLabel('Main menu');
        $siteComArea2->setAreaId('mainMenu');
        $siteComArea2->setHtmlClass('main-menu');
        $siteComArea2->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteComArea0 = new Area();
        $siteComArea0->setLabel('Header');
        $siteComArea0->setAreaId('header');
        $siteComArea0->setHtmlClass('header');
        $siteComArea0->addArea($siteComArea1);
        $siteComArea0->addArea($siteComArea2);

        $siteComArea4 = new Area();
        $siteComArea4->setLabel('Main content area 1');
        $siteComArea4->setAreaId('mainContentArea1');
        $siteComArea4->setHtmlClass('main-content-area1');
        $siteComArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $siteComArea5 = new Area();
        $siteComArea5->setLabel('module area');
        $siteComArea5->setAreaId('moduleArea');
        $siteComArea5->setHtmlClass('module-area');
        $siteComArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));
        $siteComArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));

        $siteComArea3 = new Area();
        $siteComArea3->setLabel('My main');
        $siteComArea3->setAreaId('myMain');
        $siteComArea3->setHtmlClass('my-main');
        $siteComArea3->addArea($siteComArea4);
        $siteComArea3->addArea($siteComArea5);

        $siteComArea7 = new Area();
        $siteComArea7->setLabel('Containe footer');
        $siteComArea7->setAreaId('containeFooter');
        $siteComArea7->setHtmlClass('containe-footer');
        $siteComArea7->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));

        $siteComArea6 = new Area();
        $siteComArea6->setLabel('Footer');
        $siteComArea6->setAreaId('footer');
        $siteComArea6->setHtmlClass('footer');
        $siteComArea6->addArea($siteComArea7);

        $siteCom = new Node();
        $siteCom->setNodeId('fixture_page_community');
        $siteCom->setMaxAge(1000);
        $siteCom->setNodeType('page');
        $siteCom->setName('Communauté');
        $siteCom->setSiteId('2');
        $siteCom->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteCom->setPath('-');
        $siteCom->setOrder(3);
        $siteCom->setRoutePattern('/page-community');
        $siteCom->setVersion(1);
        $siteCom->setLanguage('fr');
        $siteCom->setStatus($this->getReference('status-published'));
        $siteCom->setDeleted(false);
        $siteCom->setTemplateId('');
        $siteCom->setTheme('themePresentation');
        $siteCom->setInFooter(false);
        $siteCom->setInMenu(true);
        $siteCom->addArea($siteComArea0);
        $siteCom->addArea($siteComArea3);
        $siteCom->addArea($siteComArea6);
        $siteCom->addBlock($siteComBlock0);

        return $siteCom;
    }

    /**
     * @return Node
     */
    public function generateNodeSiteAboutUs()
    {
        $siteAboutUsBlock0 = new Block();
        $siteAboutUsBlock0->setLabel('Wysiwyg 1');
        $siteAboutUsBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteAboutUsBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='content2'>
    <h1>Interakting</h1>
    <p>
        Une agence digitale nouvelle génération classée par le Forrester parmi les 12 plus grandes agences européennes, avec un positionnement conseil et technologies.
        Une division du groupe Business&Decision
        Business & Decision est un Groupe international de services numériques,  leader de la Business Intelligence (BI) et du CRM, acteur majeur de l'e-Business.  Le Groupe contribue à la réussite des projets à forte valeur ajoutée des entreprises et accompagne ses clients dans des domaines d’innovation tels que le Big Data et le Digital. Il est reconnu pour son expertise fonctionnelle et technologique par les plus grands éditeurs de logiciels du marché avec lesquels il a noué des partenariats. Fort d’une expertise unique dans ses domaines de spécialisation, Business & Decision offre des solutions adaptées à des secteurs d’activité ainsi qu’à des directions métiers.
    </p>
</div>
EOF
        ));
        $siteAboutUsBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteAboutUsArea1 = new Area();
        $siteAboutUsArea1->setLabel('Logo');
        $siteAboutUsArea1->setAreaId('logo');
        $siteAboutUsArea1->setHtmlClass('logo');
        $siteAboutUsArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $siteAboutUsArea2 = new Area();
        $siteAboutUsArea2->setLabel('Main menu');
        $siteAboutUsArea2->setAreaId('mainMenu');
        $siteAboutUsArea2->setHtmlClass('main-menu');
        $siteAboutUsArea2->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteAboutUsArea0 = new Area();
        $siteAboutUsArea0->setLabel('Header');
        $siteAboutUsArea0->setAreaId('header');
        $siteAboutUsArea0->setHtmlClass('header');
        $siteAboutUsArea0->addArea($siteAboutUsArea1);
        $siteAboutUsArea0->addArea($siteAboutUsArea2);

        $siteAboutUsArea4 = new Area();
        $siteAboutUsArea4->setLabel('Main content area 1');
        $siteAboutUsArea4->setAreaId('mainContentArea1');
        $siteAboutUsArea4->setHtmlClass('main-content-area1');
        $siteAboutUsArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $siteAboutUsArea5 = new Area();
        $siteAboutUsArea5->setLabel('Module area');
        $siteAboutUsArea5->setAreaId('moduleArea');
        $siteAboutUsArea5->setHtmlClass('module-area');
        $siteAboutUsArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));
        $siteAboutUsArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));

        $siteAboutUsArea3 = new Area();
        $siteAboutUsArea3->setLabel('My main');
        $siteAboutUsArea3->setAreaId('myMain');
        $siteAboutUsArea3->setHtmlClass('my-main');
        $siteAboutUsArea3->addArea($siteAboutUsArea4);
        $siteAboutUsArea3->addArea($siteAboutUsArea5);

        $siteAboutUsArea7 = new Area();
        $siteAboutUsArea7->setLabel('Containe footer');
        $siteAboutUsArea7->setAreaId('containeFooter');
        $siteAboutUsArea7->setHtmlClass('containe-footer');
        $siteAboutUsArea7->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));

        $siteAboutUsArea6 = new Area();
        $siteAboutUsArea6->setLabel('Footer');
        $siteAboutUsArea6->setAreaId('footer');
        $siteAboutUsArea6->setHtmlClass('footer');
        $siteAboutUsArea6->addArea($siteAboutUsArea7);

        $siteAboutUs = new Node();
        $siteAboutUs->setNodeId('fixture_page_about_us');
        $siteAboutUs->setMaxAge(1000);
        $siteAboutUs->setNodeType('page');
        $siteAboutUs->setName('A propos');
        $siteAboutUs->setSiteId('2');
        $siteAboutUs->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteAboutUs->setOrder(4);
        $siteAboutUs->setPath('-');
        $siteAboutUs->setRoutePattern('page-about-us');
        $siteAboutUs->setVersion(1);
        $siteAboutUs->setLanguage('fr');
        $siteAboutUs->setStatus($this->getReference('status-published'));
        $siteAboutUs->setDeleted(false);
        $siteAboutUs->setTemplateId('');
        $siteAboutUs->setTheme('themePresentation');
        $siteAboutUs->setInFooter(false);
        $siteAboutUs->setInMenu(false);
        $siteAboutUs->addArea($siteAboutUsArea0);
        $siteAboutUs->addArea($siteAboutUsArea3);
        $siteAboutUs->addArea($siteAboutUsArea6);
        $siteAboutUs->addBlock($siteAboutUsBlock0);

        return $siteAboutUs;
    }

    /**
     * @return Node
     */
    public function generateNodeSiteOurTeam()
    {
        $siteOurTeamBlock0 = new Block();
        $siteOurTeamBlock0->setLabel('Wysiwyg 1');
        $siteOurTeamBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteOurTeamBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='content2'>
    <h1>Our Team</h1>
    <ul>
        <li>Une agence digitale nouvelle génération classée par le Forrester parmi les 12 plus grandes agences européennes, avec un positionnement conseil et technologies.</li>
        <li>Une présence internationale, un modèle de delivery industriel, une organisation en centre de services, des plateformes near shore et off shore.</li>
        <li>pour les grands projets de transformation « digital » et pour adresser les marchés du « customer experience management), entreprise digitale et secteur public.</li>
        <li>Partenaire stratégique des grands projets, nos interventions s’inscrivent dans la durée.</li>
    </ul>
</div>
EOF
        ));
        $siteOurTeamBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteOurTeamArea1 = new Area();
        $siteOurTeamArea1->setLabel('Logo');
        $siteOurTeamArea1->setAreaId('logo');
        $siteOurTeamArea1->setHtmlClass('logo');
        $siteOurTeamArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $siteOurTeamArea2 = new Area();
        $siteOurTeamArea2->setLabel('Main menu');
        $siteOurTeamArea2->setAreaId('mainMenu');
        $siteOurTeamArea2->setHtmlClass('main-menu');
        $siteOurTeamArea2->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteOurTeamArea0 = new Area();
        $siteOurTeamArea0->setLabel('Header');
        $siteOurTeamArea0->setAreaId('header');
        $siteOurTeamArea0->setHtmlClass('header');
        $siteOurTeamArea0->addArea($siteOurTeamArea1);
        $siteOurTeamArea0->addArea($siteOurTeamArea2);

        $siteOurTeamArea4 = new Area();
        $siteOurTeamArea4->setLabel('Main content area 1');
        $siteOurTeamArea4->setAreaId('mainContentArea1');
        $siteOurTeamArea4->setHtmlClass('main-content-area1');
        $siteOurTeamArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $siteOurTeamArea5 = new Area();
        $siteOurTeamArea5->setLabel('Module area');
        $siteOurTeamArea5->setAreaId('moduleArea');
        $siteOurTeamArea5->setHtmlClass('module-area');
        $siteOurTeamArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));
        $siteOurTeamArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));

        $siteOurTeamArea3 = new Area();
        $siteOurTeamArea3->setLabel('My main');
        $siteOurTeamArea3->setAreaId('myMain');
        $siteOurTeamArea3->setHtmlClass('my-main');
        $siteOurTeamArea3->addArea($siteOurTeamArea4);
        $siteOurTeamArea3->addArea($siteOurTeamArea5);

        $siteOurTeamArea7 = new Area();
        $siteOurTeamArea7->setLabel('Containe footer');
        $siteOurTeamArea7->setAreaId('containeFooter');
        $siteOurTeamArea7->setHtmlClass('containe-footer');
        $siteOurTeamArea7->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));

        $siteOurTeamArea6 = new Area();
        $siteOurTeamArea6->setLabel('Footer');
        $siteOurTeamArea6->setAreaId('footer');
        $siteOurTeamArea6->setHtmlClass('footer');
        $siteOurTeamArea6->addArea($siteOurTeamArea7);

        $siteOurTeam = new Node();
        $siteOurTeam->setNodeId('fixture_page_our_team');
        $siteOurTeam->setMaxAge(1000);
        $siteOurTeam->setNodeType('page');
        $siteOurTeam->setName('Fixture page our team');
        $siteOurTeam->setSiteId('2');
        $siteOurTeam->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteOurTeam->setOrder(5);
        $siteOurTeam->setPath('-');
        $siteOurTeam->setRoutePattern('/page-our-team');
        $siteOurTeam->setVersion(1);
        $siteOurTeam->setLanguage('fr');
        $siteOurTeam->setStatus($this->getReference('status-published'));
        $siteOurTeam->setDeleted(false);
        $siteOurTeam->setTemplateId('');
        $siteOurTeam->setTheme('themePresentation');
        $siteOurTeam->setInFooter(false);
        $siteOurTeam->setInMenu(false);
        $siteOurTeam->addArea($siteOurTeamArea0);
        $siteOurTeam->addArea($siteOurTeamArea3);
        $siteOurTeam->addArea($siteOurTeamArea6);
        $siteOurTeam->addBlock($siteOurTeamBlock0);

        return $siteOurTeam;
    }

    /**
     * @return Node
     */
    public function generateNodeSiteNews()
    {
        $siteNewsBlock0 = new Block();
        $siteNewsBlock0->setLabel('Wysiwyg 1');
        $siteNewsBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteNewsBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class="content2">
    <h1>Actu</h1>
    <article>
        <h2>ZADIG & VOLTAIRE PUBLIE SES MAGAZINES SUR IPAD AVEC INTERAKTING</h2>
        <p>
            Zadig & Voltaire étend et enrichit ses activités commerciales à travers des sites web marchands, les réseaux sociaux et des applications mobiles.
            L’équipe Marketing Digital de Zadig & Voltaire a opté d’une part pour le développement d’un site mobile, dupliqué du site marchand internet,
             et d’autre part d’une application iPad pour publier des magazines dérivés de ses catalogues papier. L’objectif de l’application iPad consiste à allier graphisme,
              ergonomie, interactivité, géolocalisation et e-Commerce pour ainsi développer la visibilité de la marque tout en créant une approche inédite du e-shopping.
        </p>
        <h2>FUN DISTINGUÉ PAR LE GRAND PRIX DES LECTEURS D’ACTEURS PUBLICS COMME LA MEILLEURE INITIATIVE PUBLIQUE DE L’ANNÉE!</h2>
        <p>
            Le site du Ministère de l’Enseignement supérieur et de la Recherche,
            <a href='http://businessdecision.us3.list-manage2.com/track/click?u=cf8b0e95565b3f0a524bea0a6&id=b321193023&e=3ec73c652f'>
            http://www.france-universite-numerique.fr</a>, pour lequel Interakting a réalisé l’ensemble de l’intégration HTML,
            a remporté le prix des meilleurs initiatives de l’année 2013 par le magazine Acteurs Publics.
            Ces Victoires mettent en lumière celles et ceux qui, chaque jour, agissent pour assurer le meilleur du service au public.
        </p>
    </article>
</div>
EOF
));
        $siteNewsBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteNewsArea1 = new Area();
        $siteNewsArea1->setLabel('Logo');
        $siteNewsArea1->setAreaId('logo');
        $siteNewsArea1->setHtmlClass('logo');
        $siteNewsArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $siteNewsArea2 = new Area();
        $siteNewsArea2->setLabel('Main menu');
        $siteNewsArea2->setAreaId('mainMenu');
        $siteNewsArea2->setHtmlClass('main-menu');
        $siteNewsArea2->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteNewsArea0 = new Area();
        $siteNewsArea0->setLabel('Header');
        $siteNewsArea0->setAreaId('header');
        $siteNewsArea0->setHtmlClass('header');
        $siteNewsArea0->addArea($siteNewsArea1);
        $siteNewsArea0->addArea($siteNewsArea2);

        $siteNewsArea4 = new Area();
        $siteNewsArea4->setLabel('Main content area 1');
        $siteNewsArea4->setAreaId('mainContentArea1');
        $siteNewsArea4->setHtmlClass('main-content-area1');
        $siteNewsArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $siteNewsArea5 = new Area();
        $siteNewsArea5->setLabel('Module area');
        $siteNewsArea5->setAreaId('moduleArea');
        $siteNewsArea5->setHtmlClass('module-area');
        $siteNewsArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));
        $siteNewsArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));

        $siteNewsArea3 = new Area();
        $siteNewsArea3->setLabel('My main');
        $siteNewsArea3->setAreaId('myMain');
        $siteNewsArea3->setHtmlClass('my-main');
        $siteNewsArea3->addArea($siteNewsArea4);
        $siteNewsArea3->addArea($siteNewsArea5);

        $siteNewsArea7 = new Area();
        $siteNewsArea7->setLabel('Containe footer');
        $siteNewsArea7->setAreaId('containeFooter');
        $siteNewsArea7->setHtmlClass("containe-footer");
        $siteNewsArea7->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));

        $siteNewsArea6 = new Area();
        $siteNewsArea6->setLabel('Footer');
        $siteNewsArea6->setAreaId('footer');
        $siteNewsArea6->setHtmlClass('footer');
        $siteNewsArea6->addArea($siteNewsArea7);

        $siteNews = new Node();
        $siteNews->setNodeId('fixture_page_news');
        $siteNews->setMaxAge(1000);
        $siteNews->setNodeType('page');
        $siteNews->setName('Fixture page news');
        $siteNews->setSiteId('2');
        $siteNews->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteNews->setOrder(6);
        $siteNews->setPath('-');
        $siteNews->setRoutePattern('/page-our-news');
        $siteNews->setVersion(1);
        $siteNews->setLanguage('fr');
        $siteNews->setStatus($this->getReference('status-published'));
        $siteNews->setDeleted(false);
        $siteNews->setTemplateId('');
        $siteNews->setTheme('themePresentation');
        $siteNews->setInFooter(false);
        $siteNews->setInMenu(false);
        $siteNews->addArea($siteNewsArea0);
        $siteNews->addArea($siteNewsArea3);
        $siteNews->addArea($siteNewsArea6);
        $siteNews->addBlock($siteNewsBlock0);

        return $siteNews;
    }

    /**
     * @return Node
     */
    public function generateNodeSiteJoinUs()
    {
        $siteJoinUsBlock0 = new Block();
        $siteJoinUsBlock0->setLabel('Wysiwyg 1');
        $siteJoinUsBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteJoinUsBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='content2'>
    <div class="annonce" id='annonce'>
        <h1>Nous rejoindre</h1>
        <p>Vous êtes un passionné d’Internet en général et du Web en particulier?</p>
        <p>Vous avez une expérience significative dans les domaines suivants:
        <strong>Développement&nbsp;web&nbsp;sur&nbsp;le&nbsp;framework&nbsp;Symfony&nbsp;2
        <p>Votre profil est susceptible de nous intéresser.</p>
    </div>
    <div class="contact annonce" id='form'>
        <table border='0'>
            <tbody>
                <tr>
                    <td valign='top'>Nom</td>
                    <td> <input type='text' placeholder='Votre nom' required/>
                    </td>
                </tr>
                <tr>
                    <td valign='top'>Société</td>
                    <td> <input type='text' placeholder='Votre société'/> </td>
                </tr>
                <tr>
                    <td valign='top'>Email</td>
                    <td><input type='email' placeholder='Votre e-mail' required/></td>
                </tr>
                <tr>
                    <td valign='top'>Téléphone</td>
                    <td><input type='tel' placeholder='Votre téléphone' required/></td>
                </tr>
                <tr>
                    <td valign='top'>Message</td>
                    <td><textarea  rows='10' cols='40' placeholder='Votre message' required></textarea></td>
                </tr>
                <tr>
                    <td valign='top'>CV</td>
                    <td><input type='file' /></td>
                </tr>
                <tr>
                     <td><input type='submit' value='OK'/></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
EOF
        ));
        $siteJoinUsBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteJoinUsArea1 = new Area();
        $siteJoinUsArea1->setLabel('Logo');
        $siteJoinUsArea1->setAreaId('logo');
        $siteJoinUsArea1->setHtmlClass('logo');
        $siteJoinUsArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $siteJoinUsArea2 = new Area();
        $siteJoinUsArea2->setLabel('Main menu');
        $siteJoinUsArea2->setAreaId('mainMenu');
        $siteJoinUsArea2->setHtmlClass('main-menu');
        $siteJoinUsArea2->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteJoinUsArea0 = new Area();
        $siteJoinUsArea0->setLabel('Header');
        $siteJoinUsArea0->setAreaId('header');
        $siteJoinUsArea0->setHtmlClass('header');
        $siteJoinUsArea0->addArea($siteJoinUsArea1);
        $siteJoinUsArea0->addArea($siteJoinUsArea2);

        $siteJoinUsArea4 = new Area();
        $siteJoinUsArea4->setLabel('Main content area 1');
        $siteJoinUsArea4->setAreaId('mainContentArea1');
        $siteJoinUsArea4->setHtmlClass('main-content-area1');
        $siteJoinUsArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $siteJoinUsArea5 = new Area();
        $siteJoinUsArea5->setLabel('Module area');
        $siteJoinUsArea5->setAreaId('moduleArea');
        $siteJoinUsArea5->setHtmlClass('module-area');
        $siteJoinUsArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));
        $siteJoinUsArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));

        $siteJoinUsArea3 = new Area();
        $siteJoinUsArea3->setLabel('My main');
        $siteJoinUsArea3->setAreaId('myMain');
        $siteJoinUsArea3->setHtmlClass('my-main');
        $siteJoinUsArea3->addArea($siteJoinUsArea4);
        $siteJoinUsArea3->addArea($siteJoinUsArea5);

        $siteJoinUsArea7 = new Area();
        $siteJoinUsArea7->setLabel('Containe footer');
        $siteJoinUsArea7->setAreaId('containeFooter');
        $siteJoinUsArea7->setHtmlClass('containe-footer');
        $siteJoinUsArea7->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));

        $siteJoinUsArea6 = new Area();
        $siteJoinUsArea6->setLabel('Footer');
        $siteJoinUsArea6->setAreaId('footer');
        $siteJoinUsArea6->setHtmlClass('footer');
        $siteJoinUsArea6->addArea($siteJoinUsArea7);

        $siteJoinUs = new Node();
        $siteJoinUs->setNodeId('fixture_page_join_us');
        $siteJoinUs->setMaxAge(1000);
        $siteJoinUs->setNodeType('page');
        $siteJoinUs->setName('Fixture page join us');
        $siteJoinUs->setSiteId('2');
        $siteJoinUs->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteJoinUs->setOrder(7);
        $siteJoinUs->setPath('-');
        $siteJoinUs->setRoutePattern('/page-nous-rejoindre');
        $siteJoinUs->setVersion(1);
        $siteJoinUs->setLanguage('fr');
        $siteJoinUs->setStatus($this->getReference('status-published'));
        $siteJoinUs->setDeleted(false);
        $siteJoinUs->setTemplateId('');
        $siteJoinUs->setTheme('themePresentation');
        $siteJoinUs->setInFooter(false);
        $siteJoinUs->setInMenu(false);
        $siteJoinUs->addArea($siteJoinUsArea0);
        $siteJoinUs->addArea($siteJoinUsArea3);
        $siteJoinUs->addArea($siteJoinUsArea6);
        $siteJoinUs->addBlock($siteJoinUsBlock0);

        return $siteJoinUs;
    }

    /**
     * @return Node
     */
    public function generateNodeSiteNetwork()
    {
        $siteNetworkBlock0 = new Block();
        $siteNetworkBlock0->setLabel('Wysiwyg 1');
        $siteNetworkBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteNetworkBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='content2'>
    <h1>Smart Eolas</h1>
    <p>
        Smart.eolas allie le meilleur du e-commerce et de la gestion de contenus.
        Jusqu’alors, il n’existait aucune plateforme sur le marché mêlant avec succès des fonctions avancées de gestion de contenus et de catalogue produits.
        Il y a plus de deux ans, Eolas s’est lancé dans ce vaste chantier : construire une nouvelle plateforme e-Commerce et de gestion de contenus innovante et à l’état de l’art.
    </p>
    <p>
        Smart.eolas est le fruit de 15 ans d’expérience, à la fois en tant que spécialiste du e-Commerce, du Digital Marketing et en tant qu’opérateur de solutions SaaS.
        Ce projet a ainsi fait appel aux compétences de toutes les équipes d’Eolas, notamment celles des experts en e-Tailing, issues du rachat du fonds de commerce de Proxi-Business, il y a un an.
    </p>
</div>
EOF
          ));
        $siteNetworkBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteNetworkArea1 = new Area();
        $siteNetworkArea1->setLabel('Logo');
        $siteNetworkArea1->setAreaId('logo');
        $siteNetworkArea1->setHtmlClass('logo');
        $siteNetworkArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $siteNetworkArea2 = new Area();
        $siteNetworkArea2->setLabel('Main menu');
        $siteNetworkArea2->setAreaId('mainMenu');
        $siteNetworkArea2->setHtmlClass('main-menu');
        $siteNetworkArea2->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteNetworkArea0 = new Area();
        $siteNetworkArea0->setLabel('Header');
        $siteNetworkArea0->setAreaId('header');
        $siteNetworkArea0->setHtmlClass('header');
        $siteNetworkArea0->addArea($siteNetworkArea1);
        $siteNetworkArea0->addArea($siteNetworkArea2);

        $siteNetworkArea4 = new Area();
        $siteNetworkArea4->setLabel('Main content area 1');
        $siteNetworkArea4->setAreaId('mainContentArea1');
        $siteNetworkArea4->setHtmlClass('main-content-area1');
        $siteNetworkArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $siteNetworkArea5 = new Area();
        $siteNetworkArea5->setLabel('Module area');
        $siteNetworkArea5->setAreaId('moduleArea');
        $siteNetworkArea5->setHtmlClass('module-area');
        $siteNetworkArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));
        $siteNetworkArea5->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));

        $siteNetworkArea3 = new Area();
        $siteNetworkArea3->setLabel('My main');
        $siteNetworkArea3->setAreaId('myMain');
        $siteNetworkArea3->setHtmlClass('my-main');
        $siteNetworkArea3->addArea($siteNetworkArea4);
        $siteNetworkArea3->addArea($siteNetworkArea5);

        $siteNetworkArea7 = new Area();
        $siteNetworkArea7->setLabel('Containe footer');
        $siteNetworkArea7->setAreaId('containeFooter');
        $siteNetworkArea7->setHtmlClass('containe-footer');
        $siteNetworkArea7->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));

        $siteNetworkArea6 = new Area();
        $siteNetworkArea6->setLabel('Footer');
        $siteNetworkArea6->setAreaId('footer');
        $siteNetworkArea6->setHtmlClass('footer');
        $siteNetworkArea6->addArea($siteNetworkArea7);

        $siteNetwork = new Node();
        $siteNetwork->setNodeId('fixture_page_networks');
        $siteNetwork->setMaxAge(1000);
        $siteNetwork->setNodeType('page');
        $siteNetwork->setName('Fixture page networks');
        $siteNetwork->setSiteId('2');
        $siteNetwork->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteNetwork->setOrder(8);
        $siteNetwork->setPath('-');
        $siteNetwork->setRoutePattern('/page-networks');
        $siteNetwork->setVersion(1);
        $siteNetwork->setLanguage('fr');
        $siteNetwork->setStatus($this->getReference('status-published'));
        $siteNetwork->setDeleted(false);
        $siteNetwork->setTemplateId('');
        $siteNetwork->setTheme('themePresentation');
        $siteNetwork->setInFooter(false);
        $siteNetwork->setInMenu(false);
        $siteNetwork->addArea($siteNetworkArea0);
        $siteNetwork->addArea($siteNetworkArea3);
        $siteNetwork->addArea($siteNetworkArea6);
        $siteNetwork->addBlock($siteNetworkBlock0);

        return $siteNetwork;
    }

    /*
     * @return Node
     */
    public function generateNodeSiteContact()
    {
        $siteContactBlock0 = new Block();
        $siteContactBlock0->setLabel('Wysiwyg 1');
        $siteContactBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteContactBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class="contact-information">
    <h3>Contactez-nous</h3>
    <div class="info-interakting" >
        <h4>Interakting</h4>
        <p>
            Groupe Business & Decision
            <br>153 Rue de Courcelles
            <br>75017 PARIS FRANCE
            <br><span class="fontOrange">Tél:</span> +33 1 56 21 21 21
            <br><span class="fontOrange">Fax:</span> +33 1 56 21 21 22
        </p>
    </div>
    <div class="access-interakting">
        <h4>Accès:</h4>
        <p>
            <span class="fontOrange">Metro ligne 3</span> arrêt Pereire
            <br><span class="fontOrange">RER ligne C</span> arrêt Pereire-Levallois
        </p>
    </div>
    <div class="google-maps-interakting"">
        <iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.fr/maps?f=q&amp;source=s_q&amp;hl=fr&amp;geocode=&amp;q=153+Rue+de+Courcelles+75817+Paris&amp;aq=&amp;sll=48.834414,2.499298&amp;sspn=0.523838,0.909805&amp;ie=UTF8&amp;hq=&amp;hnear=153+Rue+de+Courcelles,+75817+Paris&amp;ll=48.883747,2.298345&amp;spn=0.004088,0.007108&amp;t=m&amp;z=14&amp;output=embed"></iframe>
    </div>
</div>
EOF
        ));
        $siteContactBlock0->addArea(array('nodeId' => 0, 'areaId' => 'moduleArea'));

        $siteContactArea1 = new Area();
        $siteContactArea1->setLabel('Logo');
        $siteContactArea1->setAreaId('logo');
        $siteContactArea1->setHtmlClass('logo');
        $siteContactArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $siteContactArea2 = new Area();
        $siteContactArea2->setLabel('Main menu');
        $siteContactArea2->setAreaId('mainMenu');
        $siteContactArea2->setHtmlClass('main-menu');
        $siteContactArea2->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteContactArea0 = new Area();
        $siteContactArea0->setLabel('Header');
        $siteContactArea0->setAreaId('header');
        $siteContactArea0->setHtmlClass('header');
        $siteContactArea0->addArea($siteContactArea1);
        $siteContactArea0->addArea($siteContactArea2);

        $siteContactArea4 = new Area();
        $siteContactArea4->setLabel('Main content area 1');
        $siteContactArea4->setAreaId('mainContentArea1');
        $siteContactArea4->setHtmlClass('main-content-contact');
        $siteContactArea4->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));

        $siteContactArea5 = new Area();
        $siteContactArea5->setLabel('Module area');
        $siteContactArea5->setAreaId('moduleArea');
        $siteContactArea5->setHtmlClass('module-area-contact');
        $siteContactArea5->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $siteContactArea3 = new Area();
        $siteContactArea3->setLabel('My main');
        $siteContactArea3->setAreaId('myMain');
        $siteContactArea3->setHtmlClass('my-main');
        $siteContactArea3->addArea($siteContactArea4);
        $siteContactArea3->addArea($siteContactArea5);

        $siteContactArea7 = new Area();
        $siteContactArea7->setLabel('Containe footer');
        $siteContactArea7->setAreaId('containeFooter');
        $siteContactArea7->setHtmlClass('containe-footer');
        $siteContactArea7->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));

        $siteContactArea6 = new Area();
        $siteContactArea6->setLAbel('Footer');
        $siteContactArea6->setAreaId('footer');
        $siteContactArea6->setHtmlClass('footer');
        $siteContactArea6->addArea($siteContactArea7);

        $siteContact = new Node();
        $siteContact->setNodeId('fixture_page_contact');
        $siteContact->setMaxAge(1000);
        $siteContact->setNodeType('page');
        $siteContact->setName('Contact');
        $siteContact->setSiteId('2');
        $siteContact->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteContact->setOrder(9);
        $siteContact->setPath('-');
        $siteContact->setRoutePattern('/page-contact');
        $siteContact->setVersion(1);
        $siteContact->setLanguage('fr');
        $siteContact->setStatus($this->getReference('status-published'));
        $siteContact->setDeleted(false);
        $siteContact->setTemplateId('');
        $siteContact->setTheme('themePresentation');
        $siteContact->setInFooter(false);
        $siteContact->setInMenu(true);
        $siteContact->addArea($siteContactArea0);
        $siteContact->addArea($siteContactArea3);
        $siteContact->addArea($siteContactArea6);
        $siteContact->addBlock($siteContactBlock0);

        return $siteContact;
    }

    /**
     * @return Node
     */
    public function generateNodeSiteLegalMentions()
    {
        $siteLegalBlock0 = new Block();
        $siteLegalBlock0->setLabel('Wysiwyg 1');
        $siteLegalBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteLegalBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class="content2">
    <h1>
        <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi lacinia neque sed consequat dapibus.
Nulla hendrerit mollis nisi vitae vehicula. Maecenas viverra lacus neque, quis viverra ligula dignissim vel.
Nulla interdum pulvinar vulputate. Cras at urna sem. Nullam sed risus porta, placerat metus bibendum, commodo metus.
Donec blandit leo eros, sed convallis odio sollicitudin at.Morbi ut pulvinar lorem. Duis venenatis interdum hendrerit.
Curabitur sit amet eleifend sapien. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;
Suspendisse volutpat nulla sed eleifend malesuada. Suspendisse fringilla, est et dapibus molestie, orci leo pretium nulla,
vitae consectetur ipsum enim ut magna. Duis sagittis auctor sollicitudin. Nunc interdum, quam id varius interdum,
nulla felis blandit sapien, ac egestas lectus turpis in urna. Sed id ullamcorper nulla, quis tempor libero.
Donec aliquet neque vitae rhoncus vestibulum. Aliquam id nunc ut justo sagittis bibendum sit amet non metus.Mauris aliquam mattis sem,
in tempus eros feugiat non. Aenean vitae odio sapien. Curabitur ut luctus purus, nec vehicula nunc.
Sed massa odio, sagittis a odio eget, posuere imperdiet eros. Sed sit amet neque tempus urna rutrum egestas.
Maecenas dignissim justo orci, vitae aliquet mi gravida feugiat. Quisque ullamcorper non dui eget fringilla.
convallis condimentum placerat. Mauris bibendum libero ac neque tempus, et pharetra enim cursus. In nec porta mi.
Duis feugiat, enim nec ornare malesuada, ligula metus iaculis quam, dapibus fermentum lacus lorem ut diam.
Pellentesque condimentum ante sed augue pretium placerat. Ut venenatis, lacus vel imperdiet aliquam, enim risus rhoncus mi,
eget consequat tellus ante nec felis. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
Curabitur in erat eget leo tincidunt euismod. Sed hendrerit malesuada magna commodo porta. Suspendisse diam urna, pretium ut mi vel,
vulputate ultricies dolor. Nam eleifend accumsan nibh. Duis hendrerit ornare urna, sit amet commodo eros imperdiet nec.
Donec tristique est sit amet justo fringilla, a hendrerit ligula ultricies. Phasellus dignissim mi sit amet nibh gravida,
vitae lobortis lorem imperdiet. Praesent dolor quam, luctus sed dui eu, eleifend mattis tortor.
Curabitur varius lacus at sapien eleifend, vitae feugiat lectus mattis. In malesuada molestie turpis, et mattis ante euismod sed.
Integer interdum adipiscing purus vitae vestibulum. Proin aliquam egestas nunc, ut dictum justo lacinia quis.
Phasellus tincidunt mauris fringilla mauris hendrerit euismod.Lorem ipsum dolor sit amet, consectetur adipiscing elit.
Morbi lacinia neque sed consequat dapibus. Nulla hendrerit mollis nisi vitae vehicula.
Maecenas viverra lacus neque, quis viverra ligula dignissim vel. Nulla interdum pulvinar vulputate.Lorem ipsum dolor sit amet,
consectetur adipiscing elit. Morbi lacinia neque sed consequat dapibus. Nulla hendrerit mollis nisi vitae vehicula.
Maecenas viverra lacus neque, quis viverra ligula dignissim vel. Nulla interdum pulvinar vulputate.
        </p>
    </h1>
</div>
EOF
        ));
        $siteLegalBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteLegalBlock1 = new Block();
        $siteLegalBlock1->setLabel('Wysiwyg 2');
        $siteLegalBlock1->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteLegalBlock1->setAttributes(array(
        "htmlContent" => <<<EOF
<div class="news">
    <h3 class="blocTitle">
        <p class="titleModule">Actu</p>
    </h3>
    <div class="blockContent">
        NEWS
    </div>
</div>
EOF
        ));
        $siteLegalBlock1->addArea(array('nodeId' => 0, 'areaId' => 'moduleArea'));

        $siteLegalArea1 = new Area();
        $siteLegalArea1->setLabel('Logo');
        $siteLegalArea1->setAreaId('logo');
        $siteLegalArea1->setHtmlClass('logo');
        $siteLegalArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $siteLegalArea2 = new Area();
        $siteLegalArea2->setLabel('Main menu');
        $siteLegalArea2->setAreaId('mainMenu');
        $siteLegalArea2->setHtmlClass('main-menu');
        $siteLegalArea2->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));

        $siteLegalArea0 = new Area();
        $siteLegalArea0->setLabel('Header');
        $siteLegalArea0->setAreaId('header');
        $siteLegalArea0->setHtmlClass('header');
        $siteLegalArea0->addArea($siteLegalArea1);
        $siteLegalArea0->addArea($siteLegalArea2);

        $siteLegalArea4 = new Area();
        $siteLegalArea4->setLabel('Main content area 1');
        $siteLegalArea4->setAreaId('mainContentArea1');
        $siteLegalArea4->setHtmlClass('main-content-area1');
        $siteLegalArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $siteLegalArea5 = new Area();
        $siteLegalArea5->setLabel('Module area');
        $siteLegalArea5->setAreaId('moduleArea');
        $siteLegalArea5->setHtmlClass('module-area');
        $siteLegalArea5->addBlock(array('nodeId' => 0, 'blockId' => 1));

        $siteLegalArea3 = new Area();
        $siteLegalArea3->setLabel('My main');
        $siteLegalArea3->setAreaId('myMain');
        $siteLegalArea3->setHtmlClass('my-main');
        $siteLegalArea3->addArea($siteLegalArea4);
        $siteLegalArea3->addArea($siteLegalArea5);

        $siteLegalArea7 = new Area();
        $siteLegalArea7->setLabel('Containe footer');
        $siteLegalArea7->setAreaId('containeFooter');
        $siteLegalArea7->setHtmlClass('containe-footer');
        $siteLegalArea7->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));

        $siteLegalArea6 = new Area();
        $siteLegalArea6->setLabel('Footer');
        $siteLegalArea6->setAreaId('footer');
        $siteLegalArea6->setHtmlClass('footer');
        $siteLegalArea6->addArea($siteLegalArea7);

        $siteLegal = new Node();
        $siteLegal->setNodeId('fixture_page_legal_mentions');
        $siteLegal->setMaxAge(1000);
        $siteLegal->setNodeType('page');
        $siteLegal->setName('Fixture page legal mentions');
        $siteLegal->setSiteId('2');
        $siteLegal->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteLegal->setOrder(10);
        $siteLegal->setPath('-');
        $siteLegal->setRoutePattern('/page-legal-mentions');
        $siteLegal->setVersion(1);
        $siteLegal->setLanguage('fr');
        $siteLegal->setStatus($this->getReference('status-published'));
        $siteLegal->setDeleted(false);
        $siteLegal->setTemplateId('');
        $siteLegal->setTheme('themePresentation');
        $siteLegal->setInFooter(false);
        $siteLegal->setInMenu(false);
        $siteLegal->addArea($siteLegalArea0);
        $siteLegal->addArea($siteLegalArea3);
        $siteLegal->addArea($siteLegalArea6);
        $siteLegal->addBlock($siteLegalBlock0);
        $siteLegal->addBlock($siteLegalBlock1);

        return $siteLegal;
    }
}

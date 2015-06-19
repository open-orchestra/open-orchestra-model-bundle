<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\FooterStrategy;
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

        $siteContact = $this->generateNodeSiteContact($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteContact);
        $manager->persist($siteContact);

        $siteLegalMention = $this->generateNodeSiteLegalMentions($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteLegalMention);
        $manager->persist($siteLegalMention);

        $siteCommunity = $this->generateNodeSiteCommunity($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteCommunity);
        $manager->persist($siteCommunity);

        $siteNews = $this->generateNodeSiteNews($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteNews);
        $manager->persist($siteNews);

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
        $siteBlockLogo = new Block();
        $siteBlockLogo->setLabel('Wysiwyg logo');
        $siteBlockLogo->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteBlockLogo->setAttributes(array(
            "htmlContent" => '<a href="#" id="myLogo"> <img src="http://open-orchestra.com/media/open-orchestra-logo.png" /> </a>',
        ));
        $siteBlockLogo->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $siteBlockMainMenu = new Block();
        $siteBlockMainMenu->setLabel('Menu');
        $siteBlockMainMenu->setComponent('menu');
        $siteBlockMainMenu->setId('myMainMenu');
        $siteBlockMainMenu->setClass('my-main-menu');
        $siteBlockMainMenu->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $siteBlockFooter = new Block();
        $siteBlockFooter->setLabel('Wysiwyg footer');
        $siteBlockFooter->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
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
        $siteBlockFooterMenu->setComponent(FooterStrategy::FOOTER);

        $siteBlockContact = new Block();
        $siteBlockContact->setLabel('Contact');
        $siteBlockContact->setComponent('contact');
        $siteBlockContact->setId('myFormContact');
        $siteBlockContact->setClass('my-form-contact');
        $siteBlockContact->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $mainArea = $this->createArea('main','main','main');
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 1));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 2));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 3));

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
        $nodeTransverse->addBlock($siteBlockLogo);
        $nodeTransverse->addBlock($siteBlockMainMenu);
        $nodeTransverse->addBlock($siteBlockFooter);
        $nodeTransverse->addBlock($siteBlockFooterMenu);
        $nodeTransverse->addBlock($siteBlockContact);

        return $nodeTransverse;
    }

    /**
     * @return Node
     */
    public function generateNodeSiteHome()
    {
        $siteHomeBlock0 = new Block();
        $siteHomeBlock0->setLabel('Wysiwyg');
        $siteHomeBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteHomeBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='content2'>
    <h1>Open-Orchestra</h1>
    <p>Open-Orchestra est une puissante plateforme d’intégration web permettant d’accélérer la
    construction d’écosystèmes digitaux pérennes. Cette solution issue de l’expérience d’Interakting
    dans le développement de plateformes internationales est disponible sous licence Open-Source.</p>
    <p>Développé sur Symfony2 et mongoDB dans le strict respect des standards et bonnes pratiques du
    framework. Open-Orchestra est rapide, hautement adaptable et extensible, multi-sites et
    multi-devices.</p>
    <p>Open-Orchestra offre des fonctionnalités de CMS avancées et des composants d’intégration SI
    internes et externes hautement configurables, modulaires, taillés pour les fortes charges et la
    sécurité.</p>
    <p>Une solution ciblée :
    <ul>
        <li>Projet où «l’expérience», qu’elle soit client, collaborateur, partenaires ou distributeurs
        est au cœur de la problématique.</li>
        <li>Projet à dimension internationale nécessitant des économies d’échelle.</li>
        <li>Projets complexes et où les systèmes d’informations internes et partenaires sont fortement
         sollicités.</li>
        <li>Projet dont l’objectif est de bâtir des écosystèmes digitaux (e-commerce, communication,
        référentiel, selfcare, mobilité, distribution, …) cohérents avec des synergies fonctionnelles et
        technologiques.</li>
    </ul></p>
    <p>Notre promesse : « Economies d’échelle et mutualisation des investissements pour une expérience
    web cohérente sur tous les canaux fixe mobile, tablette, TV, bornes… »</p>
</div>
EOF
        ));
        $siteHomeBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteHomeArea1 = $this->createLogo();
        $siteHomeArea2 = $this->createMainMenu();
        $siteHomeArea0 = $this->createHeader(array($siteHomeArea1, $siteHomeArea2));
        $siteHomeArea4 = $this->createArea('Main content area 1', 'mainContentArea1', 'main-content-area1');
        $siteHomeArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $siteHomeArea5 = $this->createModuleArea();
        $siteHomeArea3 = $this->createMain(array($siteHomeArea4, $siteHomeArea5));
        $siteHomeArea7 = $this->createFooter();
        $siteHomeArea6 = $this->createFooterContainer($siteHomeArea7);

        $siteHome = $this->createBaseNode();
        $siteHome->setNodeId(NodeInterface::ROOT_NODE_ID);
        $siteHome->setName('Orchestra ?');
        $siteHome->setParentId('-');
        $siteHome->setOrder(0);
        $siteHome->setRoutePattern('/');
        $siteHome->setInFooter(false);
        $siteHome->setInMenu(true);
        $siteHome->addArea($siteHomeArea0);
        $siteHome->addArea($siteHomeArea3);
        $siteHome->addArea($siteHomeArea6);
        $siteHome->addBlock($siteHomeBlock0);
        $siteHome->setSitemapChangefreq('hourly');
        $siteHome->setSitemapPriority('0.8');

        return $siteHome;
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
    <h1>Communauté</h1>
    <p>Nous vous invitons à suivre la communanuté Open Orchestra à travers nos différents canaux de communication: </p>
    <ul>
        <li>Pour contribuer et suivre nos modifications : <a href="https://github.com/open-orchestra/"><strong>Github</strong></a></li>
        <li>Pour poser vos questions techniques : <a href="https://groups.google.com/forum/#!forum/open-orchestra"><strong>Google group</strong></a></li>
        <li>Pour suivre l'actualité de la plateforme : <a href="https://twitter.com/open_orchestra"><strong>Twitter</strong></a></li>
        <li>Pour plus de renseignements : <a href="http://open-orchestra.com/"><strong>Site officiel</strong></a></li>
    </ul>
</div>
EOF
        ));
        $siteComBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteComArea1 = $this->createLogo();
        $siteComArea2 = $this->createMainMenu();
        $siteComArea0 = $this->createHeader(array($siteComArea1,$siteComArea2));
        $siteComArea4 = $this->createArea('Main content area 1', 'mainContentArea1', 'main-content-area1');
        $siteComArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $siteComArea5 = $this->createModuleArea();
        $siteComArea3 = $this->createMain(array($siteComArea4, $siteComArea5));
        $siteComArea7 = $this->createFooter();
        $siteComArea6 = $this->createFooterContainer($siteComArea7);

        $siteCom = $this->createBaseNode();
        $siteCom->setNodeId('fixture_page_community');
        $siteCom->setName('Communauté');
        $siteCom->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteCom->setOrder(3);
        $siteCom->setRoutePattern('/page-community');
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
    public function generateNodeSiteNews()
    {
        $siteNewsBlock0 = new Block();
        $siteNewsBlock0->setLabel('Wysiwyg 1');
        $siteNewsBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteNewsBlock0->setAttributes(array(
            "htmlContent" => <<<EOF
<div class="content2">
    <h1>Actualité</h1>
    <article>
        <h2>Open Orchestra au SymfonyLive Paris 2015</h2>
        <p>
            Le SymfonyLive est l'évènement incontournable de la communauté Symfony et Open-Source
            francophone. C'est pourquoi l'équipe Open Orchestra a décidé de soutenir l'événement en
            devenant sponsor Gold de cette édition. A cette occasion, Open Orchestra s'est dévoilé
            à la communauté à l'occasion des 10 ans de Symfony devant plus de 600 participants.
        </p>
        <h2>Open Orchestra sponsorise un sfPot</h2>
        <p>
            Le 16 juin 2015, Open Orchestra à participé à l'organisation du SfPot Paris dans les
            locaux de la fondation Mozilla pour environ 100 développeurs Symfony.
        </p>
    </article>
</div>
EOF
));
        $siteNewsBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteNewsArea1 = $this->createLogo();
        $siteNewsArea2 = $this->createMainMenu();
        $siteNewsArea0 = $this->createHeader(array($siteNewsArea1, $siteNewsArea2));
        $siteNewsArea4 = $this->createArea('Main content area 1', 'mainContentArea1', 'main-content-area1');
        $siteNewsArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $siteNewsArea5 = $this->createModuleArea();
        $siteNewsArea3 = $this->createMain(array($siteNewsArea4, $siteNewsArea5));
        $siteNewsArea7 = $this->createFooter();
        $siteNewsArea6 = $this->createFooterContainer($siteNewsArea7);

        $siteNews = $this->createBaseNode();
        $siteNews->setNodeId('fixture_page_news');
        $siteNews->setName('Actualité');
        $siteNews->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteNews->setOrder(6);
        $siteNews->setRoutePattern('/page-our-news');
        $siteNews->setInFooter(false);
        $siteNews->setInMenu(true);
        $siteNews->addArea($siteNewsArea0);
        $siteNews->addArea($siteNewsArea3);
        $siteNews->addArea($siteNewsArea6);
        $siteNews->addBlock($siteNewsBlock0);

        return $siteNews;
    }

    /**
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
        <iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
        src="https://maps.google.fr/maps?f=q&amp;source=s_q&amp;hl=fr&amp;geocode=&amp;q=153+Rue+de+Courcelles+75817+Paris&amp;aq=&amp;sll=48.834414,2.499298&amp;sspn=0.523838,0.909805&amp;ie=UTF8&amp;hq=&amp;hnear=153+Rue+de+Courcelles,+75817+Paris&amp;ll=48.883747,2.298345&amp;spn=0.004088,0.007108&amp;t=m&amp;z=14&amp;output=embed"></iframe>
    </div>
</div>
EOF
        ));
        $siteContactBlock0->addArea(array('nodeId' => 0, 'areaId' => 'moduleArea'));

        $siteContactArea1 = $this->createLogo();
        $siteContactArea2 = $this->createMainMenu();
        $siteContactArea0 = $this->createHeader(array($siteContactArea1, $siteContactArea2));
        $siteContactArea4 = $this->createArea('Main content area 1', 'mainContentArea1', 'main-content-contact');
        $siteContactArea4->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));
        $siteContactArea5 = $this->createModuleArea(false, "module-area-contact");
        $siteContactArea5->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $siteContactArea3 = $this->createMain(array($siteContactArea4, $siteContactArea5));
        $siteContactArea7 = $this->createFooter();
        $siteContactArea6 = $this->createFooterContainer($siteContactArea7);

        $siteContact = $this->createBaseNode();
        $siteContact->setNodeId('fixture_page_contact');
        $siteContact->setName('Contact');
        $siteContact->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteContact->setOrder(9);
        $siteContact->setRoutePattern('/page-contact');
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
    <h1>Publisher</h1>
    <p>Open Orchestra is a registered trademark of Business & Decision</p>
    <ul>
        <li>Company name: Business & Decision S.A. (Tel.: 01 56 21 21 21)</li>
        <li>Public limited company with a capital of EUR 551,808.25</li>
        <li>Registered at RCS Paris under number: 384 518 114 B</li>
        <li>Headquarters: 153 rue de Courcelles, 75817 Paris cedex 17</li>
        <li>Publication Director: Patrick Bensabat, CEO</li>
    </ul>
</div>
EOF
        ));
        $siteLegalBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteLegalArea1 = $this->createLogo();
        $siteLegalArea2 =$this->createMainMenu();
        $siteLegalArea0 = $this->createHeader(array($siteLegalArea1, $siteLegalArea2));
        $siteLegalArea4 = $this->createArea('Main content area 1', 'mainContentArea1', 'main-content-area1' );
        $siteLegalArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $siteLegalArea3 = $this->createMain(array($siteLegalArea4));
        $siteLegalArea6 = $this->createFooter();
        $siteLegalArea5 = $this->createFooterContainer($siteLegalArea6);

        $siteLegal = $this->createBaseNode();
        $siteLegal->setNodeId('fixture_page_legal_mentions');
        $siteLegal->setName('mentions légales');
        $siteLegal->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteLegal->setOrder(10);
        $siteLegal->setRoutePattern('/page-legal-mentions');
        $siteLegal->setInFooter(true);
        $siteLegal->setInMenu(false);
        $siteLegal->addArea($siteLegalArea0);
        $siteLegal->addArea($siteLegalArea3);
        $siteLegal->addArea($siteLegalArea5);
        $siteLegal->addBlock($siteLegalBlock0);

        return $siteLegal;
    }

    /**
     * @param $label
     * @param $areaId
     * @param $htmlClass
     * @return Area
     */
    private function createArea($label, $areaId, $htmlClass = null){
        $area = new Area();
        $area->setLabel($label);
        $area->setAreaId($areaId);
        if ($htmlClass !== null){
            $area->setHtmlClass($htmlClass);
        }
        return $area;
    }

    /**
     * @return Area
     */
    private function createLogo(){
        $area = $this->createArea('Logo', 'logo', 'logo');
        $area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));
        return $area;
    }

    /**
     * @return Area
     */
    private function createMainMenu(){
        $area = $this->createArea('Main menu', 'mainMenu', 'main-menu');
        $area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1));
        return $area;
    }

    /**
     * @param array $areas
     * @return Area
     */
    private function createHeader(array $areas){
        $header = $this->createArea('Header','header','header');
        foreach($areas as $area)
            $header->addArea($area);
        return $header;
    }

    /**
     * @return Area
     */
    private function createFooter(){
        $area = $this->createArea('Containe footer','containeFooter','containe-footer');
        $area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));
        $area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2));
        return $area;
    }

    /**
     * @param $footer
     * @return Area
     */
    private function createFooterContainer($footer){
        $area = $this->createArea('Footer','footer','footer');
        $area->addArea($footer);
        return $area;
    }

    /**
     * @param bool $haveBlocks
     * @param string $class
     * @return Area
     */
    private function createModuleArea($haveBlocks = true, $class = "module-area"){
        $area = new Area();
        $area->setLabel('Module area');
        $area->setAreaId('moduleArea');
        $area->setHtmlClass($class);
        if ($haveBlocks) {
            $area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));
        }
        return $area;
    }

    /**
     * @param array $areas
     * @param bool $haveClass
     * @return Area
     */
    private function createMain(array $areas, $haveClass = true){
        $main = new Area();
        $main->setLabel('My main');
        $main->setAreaId('myMain');
        if($haveClass)
            $main->setHtmlClass('my-main');
        foreach($areas as $area)
            $main->addArea($area);
        return $main;
    }

    /**
     * @return Node
     */
    private function createBaseNode(){
        $node = new Node();
        $node->setMaxAge(1000);
        $node->setNodeType('page');
        $node->setSiteId('2');
        $node->setPath('-');
        $node->setVersion(1);
        $node->setLanguage('fr');
        $node->setStatus($this->getReference('status-published'));
        $node->setDeleted(false);
        $node->setTemplateId('');
        $node->setTheme('themePresentation');

        return $node;
    }
}

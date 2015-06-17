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

        $siteContact = $this->generateNodeSiteContact($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteContact);
        $manager->persist($siteContact);

        $siteLegalMention = $this->generateNodeSiteLegalMentions($transverseFr->getId());
        $this->addAreaRef($transverseFr, $siteLegalMention);
        $manager->persist($siteLegalMention);

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

        $siteHomeBlock2 = new Block();
        $siteHomeBlock2->setLabel('Wysiwyg 2');
        $siteHomeBlock2->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteHomeBlock2->setAttributes(array(
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
        $siteHomeBlock2->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $siteWhatBlock3 = new Block();
        $siteWhatBlock3->setLabel('What block');
        $siteWhatBlock3->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteWhatBlock3->setAttributes(array(
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
        $siteWhatBlock3->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $siteWhatBlock4 = new Block();
        $siteWhatBlock4->setLabel('Contact');
        $siteWhatBlock4->setComponent('contact');
        $siteWhatBlock4->setId('myFormContact');
        $siteWhatBlock4->setClass('my-form-contact');
        $siteWhatBlock4->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $mainArea = $this->createArea('main','main','main');
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 1));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 2));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 3));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 4));

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
        $nodeTransverse->addBlock($siteHomeBlock2);
        $nodeTransverse->addBlock($siteWhatBlock3);
        $nodeTransverse->addBlock($siteWhatBlock4);

        return $nodeTransverse;
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
     * @return Area
     */
    private function createModuleArea($haveBlocks = true){
        $area = new Area();
        $area->setLabel('Module area');
        $area->setAreaId('moduleArea');
        $area->setHtmlClass('module-area');
        if ($haveBlocks) {
            $area->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3));
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

    /**
     * @return Node
     */
    public function generateNodeSiteHome()
    {
        $siteHomeBlock0 = new Block();
        $siteHomeBlock0->setLabel('Wysiwyg 1');
        $siteHomeBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteHomeBlock0->setAttributes(array(
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
        $siteHomeBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea2'));

        $siteHomeArea1 = $this->createLogo();
        $siteHomeArea2 = $this->createMainMenu();
        $siteHomeArea0 = $this->createHeader(array($siteHomeArea1,$siteHomeArea2));
        $siteHomeArea4 = $this->createArea('Main content area 2','mainContentArea2','main-content-area2');
        $siteHomeArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $siteHomeArea3 = $this->createMain(array($siteHomeArea4), false);
        $siteHomeFooter = $this->createFooter();
        $siteHomeContainerFooter = $this->createFooterContainer($siteHomeFooter);

        $siteHome = $this->createBaseNode();
        $siteHome->setNodeId(NodeInterface::ROOT_NODE_ID);
        $siteHome->setName('Home');
        $siteHome->setParentId('-');
        $siteHome->setRoutePattern('/');
        $siteHome->setInFooter(false);
        $siteHome->setInMenu(true);
        $siteHome->addArea($siteHomeArea0);
        $siteHome->addArea($siteHomeArea3);
        $siteHome->addArea($siteHomeContainerFooter);
        $siteHome->addBlock($siteHomeBlock0);
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

        $siteWhatArea1 = $this->createLogo();
        $siteWhatArea2 = $this->createMainMenu();
        $siteWhatArea0 = $this->createHeader(array($siteWhatArea1, $siteWhatArea2));
        $siteWhatArea4 = $this->createArea('Main content area 1', 'mainContentArea1', 'main-content-area1');
        $siteWhatArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $siteWhatArea5 = $this->createModuleArea();
        $siteWhatArea3 = $this->createMain(array($siteWhatArea4, $siteWhatArea5));
        $siteWhatArea7 = $this->createFooter();
        $siteWhatArea6 = $this->createFooterContainer($siteWhatArea7);

        $siteWhat = $this->createBaseNode();
        $siteWhat->setNodeId('fixture_page_what_is_orchestra');
        $siteWhat->setName('Orchestra ?');
        $siteWhat->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteWhat->setOrder(0);
        $siteWhat->setRoutePattern('/page-what-is-orchestra');
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
        $siteNews->setName('Fixture page news');
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
        <iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.fr/maps?f=q&amp;source=s_q&amp;hl=fr&amp;geocode=&amp;q=153+Rue+de+Courcelles+75817+Paris&amp;aq=&amp;sll=48.834414,2.499298&amp;sspn=0.523838,0.909805&amp;ie=UTF8&amp;hq=&amp;hnear=153+Rue+de+Courcelles,+75817+Paris&amp;ll=48.883747,2.298345&amp;spn=0.004088,0.007108&amp;t=m&amp;z=14&amp;output=embed"></iframe>
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
        $siteContactArea5 = $this->createModuleArea(false);
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

        $siteLegalArea1 = $this->createLogo();
        $siteLegalArea2 =$this->createMainMenu();
        $siteLegalArea0 = $this->createHeader(array($siteLegalArea1, $siteLegalArea2));
        $siteLegalArea4 = $this->createArea('Main content area 1', 'mainContentArea1', 'main-content-area1' );
        $siteLegalArea4->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $siteLegalArea5 = $this->createModuleArea(false);
        $siteLegalArea5->addBlock(array('nodeId' => 0, 'blockId' => 1));
        $siteLegalArea3 = $this->createMain(array($siteLegalArea4, $siteLegalArea5));
        $siteLegalArea7 = $this->createFooter();
        $siteLegalArea6 = $this->createFooterContainer($siteLegalArea7);

        $siteLegal = $this->createBaseNode();
        $siteLegal->setNodeId('fixture_page_legal_mentions');
        $siteLegal->setName('Fixture page legal mentions');
        $siteLegal->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteLegal->setOrder(10);
        $siteLegal->setRoutePattern('/page-legal-mentions');
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

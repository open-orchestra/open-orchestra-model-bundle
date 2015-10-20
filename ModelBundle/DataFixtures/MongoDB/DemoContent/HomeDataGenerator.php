<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class HomeDataGenerator
 */
class HomeDataGenerator extends AbstractDataGenerator
{
    /**
     * @return Node
     */
    protected function generateNodeFr()
    {
        $htmlContent = <<<EOF
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
EOF;
        $routePattern = "/";
        $language = "fr";

        return $this->generateNodeGlobal($htmlContent, $language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeEn()
    {
        $htmlContent = <<<EOF
<div class='content2'>
    <h1>Open-Orchestra</h1>
    <p>Open Orchestra is a powerful web integration platform for accelerating
    the construction of perennial digital ecosystems. This solution outcome of the Interakting experience
    in the development of international platforms is available in open source license.</p>
    <p>Developed with Symfony2 and MongoDB in strict compliance to the standards and best practices
    of the framework. Open Orchestra is fast, highly adaptable and expandable, multi-site and multi-devices.</p>
    <p>A targeted solution:
    <ul>
        <li>Project where experience, whether customer, contributor, partner or distributor is at the heart of the problematic.</li>
        <li>Project with a international dimension requiring economies of scale.</li>
        <li>Complex projects where internal information systems and partners are highly asked.</li>
        <li>Project which aims to build digital ecosystems (e-commerce, communication, reference, selfcare, mobility, distribution, ...)
        consistent with the functional and technological synergies.</li>
    </ul></p>
    <p>Our promise: « Economies of scale and pooling of investments for a consistent
    web experience across all channels fixed mobile, tablet, TV, bollards ... »</p>
</div>
EOF;
        $routePattern = "en";
        $language = "en";

        return $this->generateNodeGlobal($htmlContent, $language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeDe()
    {
        $htmlContent = <<<EOF
<div class='content2'>
    <h1>Open-Orchestra</h1>
    <p>Offene Orchestra ist ein leistungsfähiges Web-Integrationsplattform zur Beschleunigung der Bau von mehrjährige digitale Ökosysteme.
     Diese Lösung von Interakting Erfahrung in der Entwicklung der internationalen Plattformen ist verfügbar unter der Open Source Lizenz.</p>
    <p>In strikter Übereinstimmung mit den Standards und Best Practices Framework entwickelt auf Symfony2 und MongoDB.
    Offene Orchestra ist schnell, sehr anpassungsfähig und erweiterbar, Multi-Site und Multi-Geräte.</p>
    <p>Offene Orchestra CMS bietet erweiterte Funktionen und SI internen und externen Integrationskomponenten
    in hohem Maße konfigurierbar, modular, hohen Belastungen und Sicherheit zugeschnitten.</p>
    <p>Eine gezielte Lösung :
    <ul>
        <li>Projekt, wo «Erfahrung», ob Kunde, Mitarbeiter, Partner oder Händler ist der Kern des Problems.</li>
        <li>Internationale Projekt Skaleneffekte erfordern.</li>
        <li>Komplexe Projekte, bei denen interne Informationssysteme und Partner sind hoch beanspruchte.</li>
        <li>Projekt, dessen Ziel ist es, digitale Ökosysteme (E-Commerce, Kommunikation, Referenz, Selbst-Pflege, Mobilität, Vertrieb, ...),
        die mit den funktionalen und technologischen Synergien aufzubauen.</li>
    </ul></p>
    <p>Unser versprechen : « Skaleneffekte und die Bündelung von Investitionen für eine konsistente Web-Erfahrung auf allen Kanälen Fixed Mobile,
    Tablet, TV, Poller ... »</p>
</div>
EOF;
        $routePattern = "de";
        $language = "de";

        return $this->generateNodeGlobal($htmlContent, $language, $routePattern);
    }

    /**
     * @param string $htmlContent
     * @param string $language
     * @param string $routePattern
     *
     * @return Node
     */
    protected function generateNodeGlobal($htmlContent, $language, $routePattern)
    {
        $siteHomeBlock0 = new Block();
        $siteHomeBlock0->setLabel('Wysiwyg');
        $siteHomeBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteHomeBlock0->setAttributes(array(
            "htmlContent" => $htmlContent
        ));
        $siteHomeBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteHomeArea0 = $this->createHeader();
        $siteHomeArea4 = $this->createArea('Main content area 1', 'mainContentArea1', 'main-content-area1');
        $siteHomeArea4->addBlock(array('nodeId' => 0, 'blockId' => 1));
        $siteHomeArea5 = $this->createModuleArea();
        $siteHomeArea3 = $this->createMain(array($siteHomeArea4, $siteHomeArea5));
        $siteHomeArea6 = $this->createFooter();

        $siteHome = $this->createBaseNode();
        $siteHome->setLanguage($language);
        $siteHome->setNodeId(NodeInterface::ROOT_NODE_ID);
        $siteHome->setName('Orchestra ?');
        $siteHome->setCreatedBy('fake_admin');
        $siteHome->setParentId('-');
        $siteHome->setOrder(0);
        $siteHome->setRoutePattern($routePattern);
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
}

<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class LegalDataGenerator
 */
class LegalDataGenerator extends AbstractDataGenerator
{
    /**
     * @return Node
     */
    protected function generateNodeFr()
    {
        $htmlContent = <<<EOF
<div class="content2">
    <h1>Entreprise</h1>
    <p>Open Orchestra est une marque déposée de Business & Decision</p>
    <ul>
        <li>Dénomination sociale : Business & Decision S.A. (tél : 01 56 21 21 21)</li>
        <li>Société anonyme au capital de 551 808,25 €</li>
        <li>Enregistré au RCS Paris : 384 518 114 B</li>
        <li>Siége social: 153 rue de Courcelles, 75817 Paris cedex 17</li>
        <li>Directeur de publication: Patrick Bensabat, PDG</li>
    </ul>
</div>
EOF;
        $name = "Mentions Légales";
        $language = "fr";
        $routePattern = "mentions-legales";

        return $this->generateNodeGlobal($htmlContent, $name, $language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeEn()
    {
        $htmlContent = <<<EOF
<div class="content2">
    <h1>&Eacute;diteur</h1>
    <p>Open Orchestra is a registered trademark of Business & Decision</p>
    <ul>
        <li>Company name: Business & Decision S.A. (Tel.: 01 56 21 21 21)</li>
        <li>Public limited company with a capital of EUR 551,808.25</li>
        <li>Registered at RCS Paris under number: 384 518 114 B</li>
        <li>Headquarters: 153 rue de Courcelles, 75817 Paris cedex 17</li>
        <li>Publication Director: Patrick Bensabat, CEO</li>
    </ul>
</div>
EOF;
        $name = "Legal Notice";
        $language = "en";
        $routePattern = "legal-page";

        return $this->generateNodeGlobal($htmlContent, $name, $language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeDe()
    {
        $htmlContent = <<<EOF
<div class="content2">
    <h1>Geschäft</h1>
    <p>Offene Orchester ist ein eingetragenes Warenzeichen von Business & Decision</p>
    <ul>
        <li>Firmenname : Business & Decision S.A. (tél : 01 56 21 21 21)</li>
        <li>Aktiengesellschaft mit einem Kapital von 551 808,25 €</li>
        <li>Aufgenommen in RCS Paris : 384 518 114 B</li>
        <li>Sitz der Gesellschaft : 153 rue de Courcelles, 75817 Paris cedex 17</li>
        <li>Herausgeber : Patrick Bensabat, PDG</li>
    </ul>
</div>
EOF;
        $name = "Rechtliche Hinweise";
        $language = "de";
        $routePattern = "rechtliche-hinweise";

        return $this->generateNodeGlobal($htmlContent, $name, $language, $routePattern);
    }

    /**
     * @param string $htmlContent
     * @param string $name
     * @param string $language
     * @param string $routePattern
     *
     * @return Node
     */
    protected function generateNodeGlobal($htmlContent, $name, $language, $routePattern)
    {
        $siteLegalBlock0 = new Block();
        $siteLegalBlock0->setLabel('Wysiwyg 1');
        $siteLegalBlock0->setComponent(TinyMCEWysiwygStrategy::NAME);
        $siteLegalBlock0->setAttributes(array(
            "htmlContent" => $htmlContent));
        $siteLegalBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteLegalArea0 = $this->createHeader();
        $siteLegalArea4 = $this->createColumnArea('Main content area 1', 'mainContentArea1', 'main-content-area1' );
        $siteLegalArea4->addBlock(array('nodeId' => 0, 'blockId' => 1));
        $siteLegalArea3 = $this->createMain(array($siteLegalArea4));
        $siteLegalArea5 = $this->createFooter();

        $siteLegal = $this->createBaseNode();
        $siteLegal->setNodeId('fixture_page_legal_mentions');
        $siteLegal->setName($name);
        $siteLegal->setBoLabel("Legal Notice");
        $siteLegal->setLanguage($language);
        $siteLegal->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteLegal->setOrder(10);
        $siteLegal->setRoutePattern($routePattern);
        $siteLegal->setInFooter(true);
        $siteLegal->setInMenu(false);

        $rootArea = $siteLegal->getArea();
        $rootArea->addArea($siteLegalArea0);
        $rootArea->addArea($siteLegalArea3);
        $rootArea->addArea($siteLegalArea5);
        $siteLegal->addBlock($siteLegalBlock0);

        return $siteLegal;
    }
}

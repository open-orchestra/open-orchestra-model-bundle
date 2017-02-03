<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelBundle\Document\Area;

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
        $nodeLegalBlock = new Block();
        $nodeLegalBlock->setLabel('Wysiwyg');
        $nodeLegalBlock->setLanguage($language);
        $nodeLegalBlock->setComponent(TinyMCEWysiwygStrategy::NAME);
        $nodeLegalBlock->setAttributes(array(
            "htmlContent" => $htmlContent
        ));

        $nodeLegalBlock = $this->generateBlock($nodeLegalBlock);

        $main = new Area();
        $main->addBlock($nodeLegalBlock);

        $header = $this->createHeader($language);

        $footer = $this->createFooter($language);

        $nodeLegal = $this->createBaseNode();
        $nodeLegal->setArea('main', $main);
        $nodeLegal->setArea('footer', $footer);
        $nodeLegal->setArea('header', $header);

        $nodeLegal->setNodeId('fixture_page_legal_mentions');
        $nodeLegal->setName($name);
        $nodeLegal->setVersionName($this->getVersionName($nodeLegal));
        $nodeLegal->setLanguage($language);
        $nodeLegal->setParentId(NodeInterface::ROOT_NODE_ID);
        $nodeLegal->setOrder(10);
        $nodeLegal->setRoutePattern($routePattern);
        $nodeLegal->setInFooter(true);
        $nodeLegal->setInMenu(false);

        return $nodeLegal;
    }
}

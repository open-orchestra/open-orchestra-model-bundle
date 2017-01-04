<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelBundle\Document\Area;

/**
 * Class AutoPublishDataGenerator
 */
class AutoPublishDataGenerator extends AbstractDataGenerator
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
        $name = "Dépublication auto";
        $language = "fr";
        $routePattern = "depublication-auto";

        $node = $this->generateNodeGlobal($htmlContent, $name, $language, $routePattern);
        $node->setUnpublishDate(\DateTime::createFromFormat('j-M-Y', '28-Feb-2016'));

        return $node;
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
        $name = "Auto publish";
        $language = "en";
        $routePattern = "auto-publish";

        $node = $this->generateNodeGlobal($htmlContent, $name, $language, $routePattern);
        $node->setPublishDate(\DateTime::createFromFormat('j-M-Y', '01-Jan-2016'));
        $node->setStatus($this->fixture->getReference('status-pending'));

        return $node;
    }

    /**
     * Useless method here
     */
    protected function generateNodeDe()
    {
        $htmlContent = <<<EOF
<div class="content2">
    <h1>Herausgeber</h1>
    <p>Open Orchestra ist ein eingetragenes Warenzeichen von Business & Decision</p>
    <ul>
        <li>Firmenname: Unternehmen & Entscheidung S.A. (Tel .: 01 56 21 21 21)</li>
        <li>Aktiengesellschaft mit einem Kapital von EUR 551.808,25</li>
        <li>Eingetragen bei RCS Paris unter der Nummer: 384 518 114 B</li>
        <li>Hauptsitz: 153 rue de Courcelles, 75817 Paris cedex 17</li>
        <li>Herausgeber: Patrick Bensabat, CEO</li>
    </ul>
</div>
EOF;
        $name = "Automatisch veröffentlichen";
        $language = "de";
        $routePattern = "auto-publish";

        $node = $this->generateNodeGlobal($htmlContent, $name, $language, $routePattern);
        $node->setPublishDate(\DateTime::createFromFormat('j-M-Y', '01-Jan-2016'));
        $node->setStatus($this->fixture->getReference('status-pending'));

        return $node;
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
        $nodeAutoPublishBlock = new Block();
        $nodeAutoPublishBlock->setLabel('Wysiwyg');
        $nodeAutoPublishBlock->setLanguage($language);
        $nodeAutoPublishBlock->setComponent(TinyMCEWysiwygStrategy::NAME);
        $nodeAutoPublishBlock->setAttributes(array(
            "htmlContent" => $htmlContent
        ));

        $nodeAutoPublishBlock = $this->generateBlock($nodeAutoPublishBlock);

        $main = new Area();
        $main->addBlock($nodeAutoPublishBlock);

        $header = $this->createHeader($language);

        $footer = $this->createFooter($language);

        $nodeAutoPublish = $this->createBaseNode();
        $nodeAutoPublish->setArea('main', $main);
        $nodeAutoPublish->setArea('footer', $footer);
        $nodeAutoPublish->setArea('header', $header);

        $nodeAutoPublish->setNodeId('fixture_auto_unpublish');
        $nodeAutoPublish->setName($name);
        $nodeAutoPublish->setLanguage($language);
        $nodeAutoPublish->setParentId(NodeInterface::ROOT_NODE_ID);
        $nodeAutoPublish->setOrder(15);
        $nodeAutoPublish->setRoutePattern($routePattern);
        $nodeAutoPublish->setInFooter(false);
        $nodeAutoPublish->setInMenu(false);

        return $nodeAutoPublish;
    }
}

<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

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
        $node->setStatus($this->references['status-pending']);

        return $node;
    }

    /**
     * Useless method here
     */
    protected function generateNodeDe()
    {
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
        $autoPublishBlock0 = new Block();
        $autoPublishBlock0->setLabel('Wysiwyg 1');
        $autoPublishBlock0->setComponent(TinyMCEWysiwygStrategy::NAME);
        $autoPublishBlock0->setAttributes(array(
            "htmlContent" => $htmlContent));
        $autoPublishBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $autoPublishArea0 = $this->createHeader();
        $autoPublishArea4 = $this->createColumnArea('Main content area 1', 'mainContentArea1', 'main-content-area1' );
        $autoPublishArea4->addBlock(array('nodeId' => 0, 'blockId' => 1, 'blockPrivate' => false));
        $autoPublishArea3 = $this->createMain(array($autoPublishArea4));
        $autoPublishArea5 = $this->createFooter();

        $autoPublish = $this->createBaseNode();
        $autoPublish->setNodeId('fixture_auto_unpublish');
        $autoPublish->setName($name);
        $autoPublish->setBoLabel("Auto unpublish");
        $autoPublish->setLanguage($language);
        $autoPublish->setParentId(NodeInterface::ROOT_NODE_ID);
        $autoPublish->setOrder(15);
        $autoPublish->setRoutePattern($routePattern);
        $autoPublish->setInFooter(true);
        $autoPublish->setInMenu(false);

        $rootArea = $autoPublish->getRootArea();
        $rootArea->addArea($autoPublishArea0);
        $rootArea->addArea($autoPublishArea3);
        $rootArea->addArea($autoPublishArea5);
        $autoPublish->addBlock($autoPublishBlock0);

        $autoPublish->setInFooter(false);

        return $autoPublish;
    }
}

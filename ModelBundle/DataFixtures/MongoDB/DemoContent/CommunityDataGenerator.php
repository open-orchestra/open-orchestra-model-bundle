<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class CommunityDataGenerator
 */
class CommunityDataGenerator extends AbstractDataGenerator
{
    /**
     * @return Node
     */
    protected function generateNodeEn()
    {
        $htmlContent = <<<EOF
<div class='content2'>
    <h1>Community</h1>
    <p>We invite you to follow the Open Orchestra community across these different way of communication :</p>
    <ul>
        <li>To contribute and follow our change : <a href="https://github.com/open-orchestra/"><strong>Github</strong></a></li>
        <li>To ask technical questions : <a href="https://groups.google.com/forum/#!forum/open-orchestra"><strong>Google group</strong></a></li>
        <li>To follows news about the product : <a href="https://twitter.com/open_orchestra"><strong>Twitter</strong></a></li>
        <li>For more information : <a href="http://open-orchestra.com/"><strong>Official Website</strong></a></li>
    </ul>
</div>
EOF;
        $name = "Community";
        $language = "en";
        $routePattern = "page-community";

        return $this->generateNodeGlobal($htmlContent, $name, $language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeFr()
    {
         $htmlContent = <<<EOF
<div class='content2'>
    <h1>Communauté</h1>
    <p>Nous vous invitons à suivre la communauté Open Orchestra à travers nos différents canaux de communication: </p>
    <ul>
        <li>Pour contribuer et suivre nos modifications : <a href="https://github.com/open-orchestra/"><strong>Github</strong></a></li>
        <li>Pour poser vos questions techniques : <a href="https://groups.google.com/forum/#!forum/open-orchestra"><strong>Google group</strong></a></li>
        <li>Pour suivre l'actualité de la plateforme : <a href="https://twitter.com/open_orchestra"><strong>Twitter</strong></a></li>
        <li>Pour plus de renseignements : <a href="http://open-orchestra.com/"><strong>Site officiel</strong></a></li>
    </ul>
</div>
EOF;
        $name = "Communauté";
        $language = "fr";
        $routePattern = 'page-communaute';

        return $this->generateNodeGlobal($htmlContent, $name, $language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeDe()
    {
        $htmlContent = <<<EOF
<div class='content2'>
    <h1>Gemeinde</h1>
    <p>Wir laden Sie zu der Open Orchestra Gemeinschaft durch unsere verschiedenen Kommunikationskanäle zu folgen : </p>
    <ul>
        <li>Einen Beitrag zu leisten und füllen Sie Änderungen : <a href="https://github.com/open-orchestra/"><strong>Github</strong></a></li>
        <li>Technische Fragen : <a href="https://groups.google.com/forum/#!forum/open-orchestra"><strong>Google group</strong></a></li>
        <li>Für die neuesten Plattform : <a href="https://twitter.com/open_orchestra"><strong>Twitter</strong></a></li>
        <li>Weitere Informationen : <a href="http://open-orchestra.com/"><strong>offizielle Website</strong></a></li>
    </ul>
</div>
EOF;
        $name = "Gemeinde";
        $language = "de";
        $routePattern = 'seite-Gemeinde';

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
        $siteComBlock0 = new Block();
        $siteComBlock0->setLabel('Wysiwyg 1');
        $siteComBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteComBlock0->setAttributes(array("htmlContent" => $htmlContent));
        $siteComBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteComArea0 = $this->createHeader();
        $siteComArea4 = $this->createArea('Main content area 1', 'mainContentArea1', 'main-content-area1');
        $siteComArea4->addBlock(array('nodeId' => 0, 'blockId' => 1));
        $siteComArea5 = $this->createModuleArea();
        $siteComArea3 = $this->createMain(array($siteComArea4, $siteComArea5));
        $siteComArea6 = $this->createFooter();

        $siteCom = $this->createBaseNode();
        $siteCom->setNodeId('fixture_page_community');
        $siteCom->setLanguage($language);
        $siteCom->setName($name);
        $siteCom->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteCom->setOrder(3);
        $siteCom->setRoutePattern($routePattern);
        $siteCom->setTheme('themePresentation');
        $siteCom->setInFooter(false);
        $siteCom->setInMenu(true);
        $siteCom->addArea($siteComArea0);
        $siteCom->addArea($siteComArea3);
        $siteCom->addArea($siteComArea6);
        $siteCom->addBlock($siteComBlock0);

        return $siteCom;
    }
}

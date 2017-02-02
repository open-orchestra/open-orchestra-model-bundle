<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelBundle\Document\Area;

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
        <li>Weitere Informationen : <a href="http://open-orchestra.com/"><strong>Offizielle Website</strong></a></li>
    </ul>
</div>
EOF;
        $name = "Gemeinde";
        $language = "de";
        $routePattern = 'seite-gemeinde';

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
        $nodeCommunityBlock = new Block();
        $nodeCommunityBlock->setLabel('Wysiwyg');
        $nodeCommunityBlock->setLanguage($language);
        $nodeCommunityBlock->setComponent(TinyMCEWysiwygStrategy::NAME);
        $nodeCommunityBlock->setAttributes(array(
            "htmlContent" => $htmlContent
        ));

        $nodeCommunityBlock = $this->generateBlock($nodeCommunityBlock);

        $main = new Area();
        $main->addBlock($nodeCommunityBlock);

        $header = $this->createHeader($language);

        $footer = $this->createFooter($language);

        $nodeCommunity = $this->createBaseNode();
        $nodeCommunity->setArea('main', $main);
        $nodeCommunity->setArea('footer', $footer);
        $nodeCommunity->setArea('header', $header);

        $nodeCommunity->setNodeId('fixture_page_community');
        $nodeCommunity->setLanguage($language);
        $nodeCommunity->setName($name);
        $nodeCommunity->setVersionName($this->getVersionName($nodeCommunity));
        $nodeCommunity->setParentId(NodeInterface::ROOT_NODE_ID);
        $nodeCommunity->setOrder(3);
        $nodeCommunity->setRoutePattern($routePattern);
        $nodeCommunity->setTheme('themePresentation');
        $nodeCommunity->setInFooter(false);
        $nodeCommunity->setInMenu(true);

        return $nodeCommunity;
    }
}

<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class NewsDataGenerator
 */
class NewsDataGenerator extends AbstractDataGenerator
{
    /**
     * @return Node
     */
    protected function generateNodeFr()
    {
        $htmlContent = <<<EOF
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
EOF;
        $name = "Actualités";
        $language = "fr";
        $routePattern = "nos-actualites";

        return $this->generateNodeGlobal($htmlContent, $name, $language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeEn()
    {
        $htmlContent = <<<EOF
<div class="content2">
    <h1>News</h1>
    <article>
        <h2>Open Orchestra at the SymfonyLive - Paris 2015</h2>
        <p>
            SymfonyLive is the most important of the French Symfony community.  For this reason, Open Orchestra 's team
            has decide to help the event and become a Gold Sponsor for the 2015 edition. It was the opportunity to reveal
            Open Orchestra to the community for the 10 years of Symfony in front of more than six hundred entrant.
        </p>
        <h2>Open Orchestra sponsors a sfPot</h2>
        <p>
            June 16th 2015, Open Orchestra participate to the Paris SfPot organisation in the
           Mozilla foundation office for about hundred hundred 100 Symfony developers.
        </p>
    </article>
</div>
EOF;
        $name = "News";
        $language = "en";
        $routePattern = "page-news";

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
        $siteNewsBlock0 = new Block();
        $siteNewsBlock0->setLabel('Wysiwyg 1');
        $siteNewsBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteNewsBlock0->setAttributes(array("htmlContent" => $htmlContent));
        $siteNewsBlock0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $siteNewsArea0 = $this->createHeader();
        $siteNewsArea4 = $this->createArea('Main content area 1', 'mainContentArea1', 'main-content-area1');
        $siteNewsArea4->addBlock(array('nodeId' => 0, 'blockId' => 1));
        $siteNewsArea5 = $this->createModuleArea();
        $siteNewsArea3 = $this->createMain(array($siteNewsArea4, $siteNewsArea5));
        $siteNewsArea6 = $this->createFooter();

        $siteNews = $this->createBaseNode();
        $siteNews->setNodeId('fixture_page_news');
        $siteNews->setName($name);
        $siteNews->setLanguage($language);
        $siteNews->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteNews->setOrder(6);
        $siteNews->setRoutePattern($routePattern);
        $siteNews->setInFooter(false);
        $siteNews->setInMenu(true);
        $siteNews->addArea($siteNewsArea0);
        $siteNews->addArea($siteNewsArea3);
        $siteNews->addArea($siteNewsArea6);
        $siteNews->addBlock($siteNewsBlock0);

        return $siteNews;
    }
}

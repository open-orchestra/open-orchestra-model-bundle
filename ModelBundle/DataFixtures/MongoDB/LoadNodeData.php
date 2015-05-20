<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AddThisStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentListStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\GmapStrategy;
use OpenOrchestra\ModelBundle\Document\Area;
use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class LoadNodeData
 */
class LoadNodeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $transverse = $this->generateTransverse('fr');
        $manager->persist($transverse);

        $transverseEn = $this->generateTransverse('en');
        $manager->persist($transverseEn);

        $transverseEs = $this->generateTransverse('es');
        $manager->persist($transverseEs);

        $home = $this->generateNodeHome(1);
        $manager->persist($home);
        $home2 = $this->generateNodeHome(2);
        $manager->persist($home2);
        $home2 = $this->generateNodeHome(3, 'status-draft');
        $manager->persist($home2);

        $homeEn = $this->generateNodeHomeEn();
        $manager->persist($homeEn);

        $full = $this->genereFullFixture();
        $this->addAreaRef($transverse, $full);
        $manager->persist($full);

        $generic = $this->generateGenericNode();
        $manager->persist($generic);

        $aboutUs = $this->generateAboutUsNode();
        $manager->persist($aboutUs);

        $manager->persist($this->generateDeletedNode());
        $manager->persist($this->generateDeletedSonNode());

        $bd = $this->generateBdNode();
        $manager->persist($bd);

        $interakting = $this->generateInteraktingNode();
        $manager->persist($interakting);

        $contactUs = $this->generateContactUsNode();
        $manager->persist($contactUs);

        $directory = $this->generateDirectoryNode();
        $manager->persist($directory);

        $search = $this->generateSearchNode();
        $manager->persist($search);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 60;
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
     * @return NodeInterface
     */
    public function generateTransverse($language)
    {
        $homeBlock = new Block();
        $homeBlock->setLabel('Bienvenue');
        $homeBlock->setComponent('sample');
        $homeBlock->setAttributes(array(
            'title' => 'Bienvenue',
            'news' => "Bienvenu sur le site de démo issu des fixtures.",
            'author' => 'ben'
        ));
        $homeBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $mainArea = new Area();
        $mainArea->setLabel('main');
        $mainArea->setAreaId('main');
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $nodeTransverse = new Node();
        $nodeTransverse->setNodeId(NodeInterface::TRANSVERSE_NODE_ID);
        $nodeTransverse->setMaxAge(1000);
        $nodeTransverse->setNodeType(NodeInterface::TYPE_TRANSVERSE);
        $nodeTransverse->setName(NodeInterface::TRANSVERSE_NODE_ID);
        $nodeTransverse->setSiteId('1');
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
        $nodeTransverse->addBlock($homeBlock);

        return $nodeTransverse;
    }

    /**
     * @return Node
     */
    protected function generateNodeHome($version, $status = 'status-published')
    {
        $homeBlock = new Block();
        $homeBlock->setLabel('Home');
        $homeBlock->setComponent('sample');
        $homeBlock->setAttributes(array(
            'title' => 'Accueil',
            'news' => "Bienvenue sur le site de démo issu des fixtures.",
            'author' => ''
        ));
        $homeBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $loginBlock = new Block();
        $loginBlock->setLabel('Login');
        $loginBlock->setComponent('login');
        $loginBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $blocksubmenu = new Block();
        $blocksubmenu->setLabel('subMenu');
        $blocksubmenu->setComponent('sub_menu');
        $blocksubmenu->setId('idmenu');
        $blocksubmenu->setClass('sousmenu');
        $blocksubmenu->setAttributes(array(
            'nbLevel' => 2,
            'nodeName' => 'fixture_about_us',
        ));
        $blocksubmenu->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $blockLanguage = new Block();
        $blockLanguage->setLabel('languages');
        $blockLanguage->setComponent('language_list');
        $blockLanguage->setId('languages');
        $blockLanguage->setClass('languageClass');
        $blockLanguage->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $blockDailymotion = new Block();
        $blockDailymotion->setLabel('dailymotion');
        $blockDailymotion->setComponent('video');
        $blockDailymotion->setId('dailymotion');
        $blockDailymotion->setClass('dailymotionClass');
        $blockDailymotion->setAttributes(array(
            'videoType' => 'dailymotion',
            'dailymotionBackground' => '#b51b15',
            'dailymotionForeground' => '#121514',
            'dailymotionHighlight' => '#15c417',
            'dailymotionVideoId' => 'x2eci0m',
            'dailymotionChromeless' => false,
            'dailymotionAutoplay' => true,
            'dailymotionQuality' => '720',
            'dailymotionRelated' => false,
            'dailymotionHeight' => '269',
            'dailymotionWidth' => '480',
            'dailymotionInfo' => false,
            'dailymotionLogo' => false,
        ));
        $blockDailymotion->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $blockYoutube = new Block();
        $blockYoutube->setLabel('youtube');
        $blockYoutube->setComponent('video');
        $blockYoutube->setId('youtube');
        $blockYoutube->setClass('youtubeClass');
        $blockYoutube->setAttributes(array(
            'videoType' => 'youtube',
            'youtubeVideoId' => '3O-2klyE80w',
            'youtubeDisablekb' => true,
            'youtubeControls' => true,
            'youtubeShowinfo' => true,
            'youtubeAutoplay' => true,
            'youtubeHeight' => '269',
            'youtubeWidth' => '480',
            'youtubeTheme' => true,
            'youtubeColor' => true,
            'youtubeLoop' => true,
            'youtubeRel' => true,
            'youtubeFs' => true,
            'youtubeHl' => 'en',
        ));
        $blockYoutube->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $homeArea = new Area();
        $homeArea->setLabel('Main');
        $homeArea->setAreaId('main');
        $homeArea->setBlocks(array(
            array('nodeId' => 0, 'blockId' => 0),
            array('nodeId' => 0, 'blockId' => 1),
            array('nodeId' => 0, 'blockId' => 2),
            array('nodeId' => 0, 'blockId' => 3),
            array('nodeId' => 0, 'blockId' => 4),
            array('nodeId' => 0, 'blockId' => 5),
        ));

        $home = new Node();
        $home->setNodeId(NodeInterface::ROOT_NODE_ID);
        $home->setMaxAge(1000);
        $home->setNodeType(NodeInterface::TYPE_DEFAULT);
        $home->setSiteId('1');
        $home->setParentId('-');
        $home->setPath('-');
        $home->setRoutePattern('/');
        $home->setName('Fixture Home');
        $home->setVersion($version);
        $home->setLanguage('fr');
        $home->setStatus($this->getReference($status));
        $home->setDeleted(false);
        $home->setTemplateId('template_home');
        $home->setTheme('theme1');
        $home->setInMenu(true);
        $home->setInFooter(false);
        $home->addArea($homeArea);
        $home->addBlock($homeBlock);
        $home->addBlock($loginBlock);
        $home->addBlock($blocksubmenu);
        $home->addBlock($blockLanguage);
        $home->addBlock($blockDailymotion);
        $home->addBlock($blockYoutube);

        return $home;
    }

    /**
     * @return Node
     */
    protected function generateNodeHomeEn()
    {
        $homeBlock = new Block();
        $homeBlock->setLabel('Home');
        $homeBlock->setComponent('sample');
        $homeBlock->setAttributes(array(
            'title' => 'Welcome',
            'news' => "Welcome to the demo site from fixtures.",
            'author' => ''
        ));
        $homeBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $loginBlock = new Block();
        $loginBlock->setLabel('Login');
        $loginBlock->setComponent('login');
        $loginBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $blockVimeo = new Block();
        $blockVimeo->setLabel('vimeo');
        $blockVimeo->setComponent('video');
        $blockVimeo->setId('vimeo');
        $blockVimeo->setClass('vimeoClass');
        $blockVimeo->setAttributes(array(
            'videoType' => 'vimeo',
            'vimeoVideoId' => '116362234',
            'vimeoFullscreen' => true,
            'vimeoColor' => 'b51b15',
            'vimeoPortrait' => false,
            'vimeoAutoplay' => true,
            'vimeoHeight' => '269',
            'vimeoByline' => false,
            'vimeoBadge' => false,
            'vimeoWidth' => '480',
            'vimeoTitle' => false,
            'vimeoLoop' => true,
        ));
        $blockVimeo->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $homeArea = new Area();
        $homeArea->setLabel('Main');
        $homeArea->setAreaId('main');
        $homeArea->setBlocks(array(
            array('nodeId' => 0, 'blockId' => 0),
            array('nodeId' => 0, 'blockId' => 1),
            array('nodeId' => 0, 'blockId' => 2),
        ));


        $home = new Node();
        $home->setNodeId(NodeInterface::ROOT_NODE_ID);
        $home->setMaxAge(1000);
        $home->setNodeType(NodeInterface::TYPE_DEFAULT);
        $home->setSiteId('1');
        $home->setParentId('-');
        $home->setPath('-');
        $home->setRoutePattern('/');
        $home->setName('Fixture Home');
        $home->setVersion(1);
        $home->setLanguage('en');
        $home->setStatus($this->getReference('status-published'));
        $home->setDeleted(false);
        $home->setTemplateId('template_home');
        $home->setTheme('theme1');
        $home->setInMenu(true);
        $home->setInFooter(false);
        $home->addArea($homeArea);
        $home->addBlock($homeBlock);
        $home->addBlock($loginBlock);
        $home->addBlock($blockVimeo);

        return $home;
    }

    /**
     * @return Node
     */
    protected function genereFullFixture()
    {
        $siteHomeArea1 = new Area();
        $siteHomeArea1->setLabel('Bienvenue');
        $siteHomeArea1->setAreaId('bienvenue');
        $siteHomeArea1->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0));

        $block0 = new Block();
        $block0->setLabel('block 1');
        $block0->setComponent('sample');
        $block0->setAttributes(array(
            'title' => 'Qui sommes-nous?',
            'author' => 'Pourquoi nous choisir ?',
            'news' => 'Nos agences'
        ));
        $block0->addArea(array('nodeId' => 0, 'areaId' => 'header'));

        $block1 = new Block();
        $block1->setLabel('block 2');
        $block1->setComponent('menu');
        $block1->setId('idmenu');
        $block1->setClass('menuclass');
        $block1->addArea(array('nodeId' => 0, 'areaId' => 'left_menu'));

        $block2 = new Block();
        $block2->setLabel('block 3');
        $block2->setComponent('sample');
        $block2->setAttributes(array(
            "title" => "News 1",
            "author" => "Donec bibendum at nibh eget imperdiet. Mauris eget justo augue. Fusce fermentum iaculis erat, sollicitudin elementum enim sodales eu. Donec a ante tortor. Suspendisse a.",
            "news" => ""
        ));
        $block2->addArea(array('nodeId' => 0, 'areaId' => 'content'));

        $block3 = new Block();
        $block3->setLabel('block 4');
        $block3->setComponent('sample');
        $block3->setAttributes(array(
            "title" => "News #2",
            "author" => "Aliquam convallis facilisis nulla, id ultricies ipsum cursus eu. Proin augue quam, iaculis id nisi ac, rutrum blandit leo. In leo ante, scelerisque tempus lacinia in, sollicitudin quis justo. Vestibulum.",
            "news" => ""
        ));
        $block3->addArea(array('nodeId' => 0, 'areaId' => 'content'));

        $block4 = new Block();
        $block4->setLabel('block 5');
        $block4->setComponent('sample');
        $block4->setAttributes(array(
            "title" => "News #3",
            "author" => "Phasellus condimentum diam placerat varius iaculis. Aenean dictum, libero in sollicitudin hendrerit, nulla mi elementum massa, eget mattis lorem enim vel magna. Fusce suscipit orci vitae vestibulum.",
            "news" => ""
        ));
        $block4->addArea(array('nodeId' => 0, 'areaId' => 'content'));

        $block5 = new Block();
        $block5->setLabel('block 6');
        $block5->setComponent('sample');
        $block5->setAttributes(array(
            'title' => '/apple-touch-icon.png',
            'author' => 'bépo',
            'news' => '',
            'image' => '/apple-touch-icon.png'
        ));
        $block5->addArea(array('nodeId' => 0, 'areaId' => 'skycrapper'));

        $block6 = new Block();
        $block6->setLabel('block 7');
        $block6->setComponent('footer');
        $block6->setId('idFooter');
        $block6->setClass('footerclass');
        $block6->addArea(array('nodeId' => 0, 'areaId' => 'footer'));

        $block7 = new Block();
        $block7->setLabel('block 8');
        $block7->setComponent('search');
        $block7->setClass('classbouton');
        $block7->setAttributes(array(
            'value' => 'Rechercher',
            'nodeId' => 'fixture_search',
            'limit' => 8
        ));
        $block7->addArea(array('nodeId' => 0, 'areaId' => 'search'));

        $headerArea = new Area();
        $headerArea->setLabel('Header');
        $headerArea->setAreaId('header');
        $headerArea->setBlocks(array(array('nodeId' => 0, 'blockId' => 0)));

        $leftMenuArea = new Area();
        $leftMenuArea->setLabel('Left menu');
        $leftMenuArea->setAreaId('left_menu');
        $leftMenuArea->setBlocks(array(array('nodeId' => 0, 'blockId' => 1)));

        $contentArea = new Area();
        $contentArea->setLabel('Content');
        $contentArea->setAreaId('content');
        $contentArea->setBlocks(array(
            array('nodeId' => 0, 'blockId' => 2),
            array('nodeId' => 0, 'blockId' => 3),
            array('nodeId' => 0, 'blockId' => 4),
        ));

        $skycrapperArea = new Area();
        $skycrapperArea->setLabel('Skycrapper');
        $skycrapperArea->setAreaId('skycrapper');
        $skycrapperArea->setBlocks(array(array('nodeId' => 0, 'blockId' => 5)));

        $mainArea = new Area();
        $mainArea->setLabel('Main');
        $mainArea->setAreaId('main');
        $mainArea->setBoDirection('v');
        $mainArea->addArea($leftMenuArea);
        $mainArea->addArea($contentArea);
        $mainArea->addArea($skycrapperArea);

        $footerArea = new Area();
        $footerArea->setLabel('Footer');
        $footerArea->setAreaId('footer');
        $footerArea->setBlocks(array(array('nodeId' => 0, 'blockId' => 6)));

        $searchArea = new Area();
        $searchArea->setLabel('Search');
        $searchArea->setAreaId('search');
        $searchArea->setBlocks(array(array('nodeId' => 0, 'blockId' => 7)));

        $full = new Node();
        $full->setNodeId('fixture_full');
        $full->setMaxAge(1000);
        $full->setNodeType(NodeInterface::TYPE_DEFAULT);
        $full->setSiteId('1');
        $full->setParentId(NodeInterface::ROOT_NODE_ID);
        $full->setPath('-');
        $full->setRoutePattern('/fixture-full');
        $full->setName('Fixture full sample');
        $full->setVersion(1);
        $full->setLanguage('fr');
        $full->setStatus($this->getReference('status-published'));
        $full->setDeleted(false);
        $full->setTemplateId('template_full');
        $full->setTheme('mixed');
        $full->setInMenu(true);
        $full->setInFooter(false);
        $full->addArea($siteHomeArea1);
        $full->addArea($headerArea);
        $full->addArea($mainArea);
        $full->addArea($footerArea);
        $full->addArea($searchArea);
        $full->addBlock($block0);
        $full->addBlock($block1);
        $full->addBlock($block2);
        $full->addBlock($block3);
        $full->addBlock($block4);
        $full->addBlock($block5);
        $full->addBlock($block6);
        $full->addBlock($block7);
        $full->setRole('ROLE_ADMIN');

        return $full;
    }

    /**
     * @return Node
     */
    protected function generateGenericNode()
    {
        $genericArea = new Area();
        $genericArea->setLabel('Generic Area');
        $genericArea->setAreaId('Generic Area');

        $node = new Node();
        $node->setNodeId('fixutre_generic');
        $node->setMaxAge(1000);
        $node->setNodeType(NodeInterface::TYPE_DEFAULT);
        $node->setSiteId('1');
        $node->setParentId(NodeInterface::ROOT_NODE_ID);
        $node->setPath('-');
        $node->setOrder(1);
        $node->setRoutePattern('/fixture-generic');
        $node->setName('Generic Node');
        $node->setVersion(1);
        $node->setLanguage('fr');
        $node->setStatus($this->getReference('status-published'));
        $node->setTemplateId('template_generic');
        $node->setDeleted(true);
        $node->setInMenu(false);
        $node->setInFooter(false);
        $node->addArea($genericArea);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateAboutUsNode()
    {
        $aboutUsBlock = new Block();
        $aboutUsBlock->setLabel('About us');
        $aboutUsBlock->setComponent('sample');
        $aboutUsBlock->setAttributes(array(
            'title' => 'Qui sommes-nous?',
            'author' => 'Pour tout savoir sur notre entreprise.',
            'news' => ''
        ));
        $aboutUsBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $contentGmap = new Block();
        $contentGmap->setLabel('gmap');
        $contentGmap->setComponent(GmapStrategy::GMAP);
        $contentGmap->setId('gmap');
        $contentGmap->setClass('gmapClass');
        $contentGmap->setAttributes(array(
            'latitude' => '48.8832139',
            'longitude' => '2.2976792',
            'zoom' => '17'
        ));
        $contentGmap->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $addThis = new Block();
        $addThis->setLabel('add this');
        $addThis->setComponent(AddThisStrategy::ADDTHIS);
        $addThis->setId('addthis');
        $addThis->setClass('addthisClass');
        $addThis->setAttributes(array(
            'pubid' => 'ra-54b3f8543eead09b',
            'addThisClass' => 'addthis_sharing_toolbox'
        ));
        $addThis->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $aboutUsArea = new Area();
        $aboutUsArea->setLabel('Main');
        $aboutUsArea->setAreaId('main');
        $aboutUsArea->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $aboutUsArea->addBlock(array('nodeId' => 0, 'blockId' => 1));
        $aboutUsArea->addBlock(array('nodeId' => 0, 'blockId' => 2 ));

        $node = new Node();
        $node->setNodeId('fixture_about_us');
        $node->setMaxAge(1000);
        $node->setNodeType(NodeInterface::TYPE_DEFAULT);
        $node->setName('Fixture About Us');
        $node->setSiteId('1');
        $node->setParentId(NodeInterface::ROOT_NODE_ID);
        $node->setPath('-');
        $node->setOrder(2);
        $node->setRoutePattern('/qui-sommes-nous');
        $node->setVersion(1);
        $node->setLanguage('fr');
        $node->setStatus($this->getReference('status-published'));
        $node->setDeleted(false);
        $node->setTemplateId('template_home');
        $node->setTheme('theme2');
        $node->setInFooter(true);
        $node->setInMenu(true);
        $node->addArea($aboutUsArea);
        $node->addBlock($aboutUsBlock);
        $node->addBlock($contentGmap);
        $node->addBlock($addThis);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateDeletedNode()
    {
        $aboutUsBlock = new Block();
        $aboutUsBlock->setLabel('About us');
        $aboutUsBlock->setComponent('sample');
        $aboutUsBlock->setAttributes(array(
            'title' => 'Qui sommes-nous?',
            'author' => 'Pour tout savoir sur notre entreprise.',
            'news' => ''
        ));
        $aboutUsBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $aboutUsArea = new Area();
        $aboutUsArea->setLabel('Main');
        $aboutUsArea->setAreaId('main');
        $aboutUsArea->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $node = new Node();
        $node->setNodeId('fixture_deleted');
        $node->setMaxAge(1000);
        $node->setNodeType(NodeInterface::TYPE_DEFAULT);
        $node->setName('Fixture deleted');
        $node->setSiteId('1');
        $node->setParentId(NodeInterface::ROOT_NODE_ID);
        $node->setPath('-');
        $node->setRoutePattern('/fixture-deleted');
        $node->setOrder(3);
        $node->setVersion(1);
        $node->setLanguage('fr');
        $node->setStatus($this->getReference('status-published'));
        $node->setDeleted(true);
        $node->setTemplateId('template_home');
        $node->setTheme('theme2');
        $node->setInFooter(true);
        $node->setInMenu(true);
        $node->addArea($aboutUsArea);
        $node->addBlock($aboutUsBlock);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateDeletedSonNode()
    {
        $aboutUsBlock = new Block();
        $aboutUsBlock->setLabel('About us');
        $aboutUsBlock->setComponent('sample');
        $aboutUsBlock->setAttributes(array(
            'title' => 'Qui sommes-nous?',
            'author' => 'Pour tout savoir sur notre entreprise.',
            'news' => ''
        ));
        $aboutUsBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $aboutUsArea = new Area();
        $aboutUsArea->setLabel('Main');
        $aboutUsArea->setAreaId('main');
        $aboutUsArea->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $aboutUs = new Node();
        $aboutUs->setNodeId('fixture_deleted_son');
        $aboutUs->setMaxAge(1000);
        $aboutUs->setNodeType(NodeInterface::TYPE_DEFAULT);
        $aboutUs->setName('Fixture deleted son');
        $aboutUs->setSiteId('1');
        $aboutUs->setParentId('fixture_deleted');
        $aboutUs->setRoutePattern('/fixture-deleted/fixture-deleted-son');
        $aboutUs->setPath('-');
        $aboutUs->setVersion(1);
        $aboutUs->setLanguage('fr');
        $aboutUs->setStatus($this->getReference('status-published'));
        $aboutUs->setDeleted(true);
        $aboutUs->setTemplateId('template_home');
        $aboutUs->setTheme('theme2');
        $aboutUs->setInFooter(true);
        $aboutUs->setInMenu(true);
        $aboutUs->addArea($aboutUsArea);
        $aboutUs->addBlock($aboutUsBlock);

        return $aboutUs;
    }

    /**
     * @return Node
     */
    protected function generateBdNode()
    {
        $bdBlock = new Block();
        $bdBlock->setLabel('B&D');
        $bdBlock->setComponent('sample');
        $bdBlock->setAttributes(array(
            'title' => 'B&D',
            'author' => 'Tout sur B&D',
            'news' => ''
        ));
        $bdBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $contentBlock = new Block();
        $contentBlock->setLabel('content news');
        $contentBlock->setComponent(ContentListStrategy::CONTENT_LIST);
        $contentBlock->setId('contentNewsList');
        $contentBlock->setClass('contentListClass');
        $contentBlock->setAttributes(array(
            'contentType' => 'news',
            'contentNodeId' => 'fixture_bd'
        ));
        $contentBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $bdArea = new Area();
        $bdArea->setLabel('Main');
        $bdArea->setAreaId('main');
        $bdArea->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $bdArea->addBlock(array('nodeId' => 0, 'blockId' => 1));

        $bd = new Node();
        $bd->setNodeId('fixture_bd');
        $bd->setMaxAge(1000);
        $bd->setNodeType(NodeInterface::TYPE_DEFAULT);
        $bd->setName('Fixture B&D');
        $bd->setSiteId('1');
        $bd->setParentId('fixture_about_us');
        $bd->setRoutePattern('/qui-sommes-nous/b-et-d');
        $bd->setPath('-');
        $bd->setVersion(1);
        $bd->setLanguage('fr');
        $bd->setStatus($this->getReference('status-published'));
        $bd->setDeleted(false);
        $bd->setTemplateId('template_home');
        $bd->setTheme('theme2');
        $bd->setInFooter(true);
        $bd->setInMenu(true);
        $bd->addArea($bdArea);
        $bd->addBlock($bdBlock);
        $bd->addBlock($contentBlock);

        return $bd;
    }

    /**
     * @return Node
     */
    protected function generateInteraktingNode()
    {
        $interaktingBlock = new Block();
        $interaktingBlock->setLabel('Interakting');
        $interaktingBlock->setComponent('sample');
        $interaktingBlock->setAttributes(array(
            'title' => 'Interakting',
            'author' => '',
            'news' => 'Des trucs sur Interakting (non versionnés)'
        ));
        $interaktingBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $interaktingArea = new Area();
        $interaktingArea->setLabel('Main');
        $interaktingArea->setAreaId('main');
        $interaktingArea->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $interakting = new Node();
        $interakting->setNodeId('fixture_interakting');
        $interakting->setMaxAge(1000);
        $interakting->setNodeType(NodeInterface::TYPE_DEFAULT);
        $interakting->setName('Fixture Interakting');
        $interakting->setSiteId('1');
        $interakting->setParentId('fixture_about_us');
        $interakting->setPath('-');
        $interakting->setOrder(1);
        $interakting->setRoutePattern('/qui-sommes-nous/interakting');
        $interakting->setVersion(1);
        $interakting->setLanguage('fr');
        $interakting->setStatus($this->getReference('status-published'));
        $interakting->setDeleted(false);
        $interakting->setTemplateId('template_home');
        $interakting->setTheme('sample');
        $interakting->setInFooter(true);
        $interakting->setInMenu(true);
        $interakting->addArea($interaktingArea);
        $interakting->addBlock($interaktingBlock);

        return $interakting;
    }

    /**
     * @return Node
     */
    protected function generateContactUsNode()
    {
        $contactUsBlock = new Block();
        $contactUsBlock->setLabel('Contact Us');
        $contactUsBlock->setComponent('sample');
        $contactUsBlock->setAttributes(array(
            'title' => 'Nous contacter',
            'author' => 'Comment nous contacter',
            'news' => 'swgsdwgh',
            'contentType' => 'news'
        ));
        $contactUsBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $contactUsArea = new Area();
        $contactUsArea->setLabel('Main');
        $contactUsArea->setAreaId('main');
        $contactUsArea->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $contactUs = new Node();
        $contactUs->setNodeId('fixture_contact_us');
        $contactUs->setMaxAge(1000);
        $contactUs->setNodeType(NodeInterface::TYPE_DEFAULT);
        $contactUs->setName('Fixture Contact Us');
        $contactUs->setSiteId('1');
        $contactUs->setParentId(NodeInterface::ROOT_NODE_ID);
        $contactUs->setPath('-');
        $contactUs->setRoutePattern('/nous-contacter');
        $contactUs->setVersion(1);
        $contactUs->setOrder(4);
        $contactUs->setLanguage('fr');
        $contactUs->setStatus($this->getReference('status-published'));
        $contactUs->setDeleted(false);
        $contactUs->setTemplateId('template_home');
        $contactUs->setTheme('theme1');
        $contactUs->setInFooter(true);
        $contactUs->setInMenu(true);
        $contactUs->addArea($contactUsArea);
        $contactUs->addBlock($contactUsBlock);

        return $contactUs;
    }

    /**
     * @return Node
     */
    protected function generateDirectoryNode()
    {
        $directoryBlock = new Block();
        $directoryBlock->setLabel('Directory');
        $directoryBlock->setComponent('sample');
        $directoryBlock->setAttributes(array(
            'title' => 'Annuaire',
            'author' => 'Le bottin mondain',
            'news' => '',
            'contentType' => 'car'
        ));
        $directoryBlock->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $directoryArea = new Area();
        $directoryArea->setLabel('Main');
        $directoryArea->setAreaId('main');
        $directoryArea->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $directory = new Node();
        $directory->setNodeId('fixture_directory');
        $directory->setMaxAge(1000);
        $directory->setNodeType(NodeInterface::TYPE_DEFAULT);
        $directory->setName('Fixture Directory');
        $directory->setSiteId('1');
        $directory->setParentId(NodeInterface::ROOT_NODE_ID);
        $directory->setPath('-');
        $directory->setOrder(5);
        $directory->setRoutePattern('/directory');
        $directory->setVersion(1);
        $directory->setLanguage('fr');
        $directory->setStatus($this->getReference('status-published'));
        $directory->setDeleted(false);
        $directory->setTemplateId('template_home');
        $directory->setTheme('fromApp');
        $directory->setInFooter(true);
        $directory->setInMenu(true);
        $directory->addArea($directoryArea);
        $directory->addBlock($directoryBlock);

        return $directory;
    }

    /**
     * @return Node
     */
    protected function generateSearchNode()
    {
        $searchBlock0 = new Block();
        $searchBlock0->setLabel('Search block');
        $searchBlock0->setComponent('sample');
        $searchBlock0->setAttributes(array(
            'title' => 'Qui somme-nous?',
            'author' => 'Pourquoi nous choisir ?',
            'news' => 'Nos agences'
        ));
        $searchBlock0->addArea(array('nodeId' => 0, 'areaId' => 'header'));

        $searchBlock1 = new Block();
        $searchBlock1->setLabel('Menu');
        $searchBlock1->setComponent('menu');
        $searchBlock1->setAttributes(array(
            'class' => 'menuClass',
            'id' => 'idmenu',
        ));
        $searchBlock1->addArea(array('nodeId' => 0, 'areaId' => 'left_menu'));

        $searchBlock2 = new Block();
        $searchBlock2->setLabel('search');
        $searchBlock2->setComponent('search');
        $searchBlock2->setClass('classbouton');
        $searchBlock2->setAttributes(array(
            'value' => 'Rechercher',
            'name' => "btnSearch",
            'nodeId' => 'fixture_search'
        ));
        $searchBlock2->addArea(array('nodeId' => 0, 'areaId' => 'content'));

        $searchBlock3 = new Block();
        $searchBlock3->setLabel('Search result');
        $searchBlock3->setComponent('search_result');
        $searchBlock3->setAttributes(array(
            'nodeId' => 'fixture_search',
            'nbdoc' => '5',
            'fielddisplayed' => array(
                "title_s", "news_t", "author_ss", "title_txt", "intro_t", "text_t", "description_t", "image_img"
            ),
            "facets" => array(
                "facetField" => array(
                    "name" =>"parent",
                    "field" => "parentId",
                    "options" => array()
                )
            ),
            "filter" => array(),
            "nbspellcheck" => "6",
            "optionsearch" => array(),
            "optionsdismax" => array(
                "fields" => array(
                    "author_s", "intro_t", "title_s"
                ),
                "boost" => array(
                    "2", "1.5", "1"
                ),
                "mm" => "75%"
            )
        ));
        $searchBlock3->addArea(array('nodeId' => 0, 'areaId' => 'content'));

        $searchBlock4 = new Block();
        $searchBlock4->setLabel('Footer');
        $searchBlock4->setComponent('footer');
        $searchBlock4->setAttributes(array(
            'id' => 'idFooter',
            'class' => 'footerClass',
        ));
        $searchBlock4->addArea(array('nodeId' => 0, 'areaId' => 'footer'));

        $searchArea0 = new Area();
        $searchArea0->setLabel('Header');
        $searchArea0->setAreaId('header');
        $searchArea0->addBlock(array('nodeId' => 0, 'blockId' => 0));

        $leftMenuArea = new Area();
        $leftMenuArea->setLabel('Left menu');
        $leftMenuArea->setAreaId('left_menu');
        $leftMenuArea->addBlock(array('nodeId' => 0, 'blockId' => 1));

        $contentArea = new Area();
        $contentArea->setLabel('Content');
        $contentArea->setAreaId('content');
        $contentArea->addBlock(array('nodeId' => 0, 'blockId' => 2));
        $contentArea->addBlock(array('nodeId' => 0, 'blockId' => 3));

        $searchArea1 = new Area();
        $searchArea1->setLabel('Main');
        $searchArea1->setAreaId('main');
        $searchArea1->setBoDirection('v');
        $searchArea1->addArea($leftMenuArea);
        $searchArea1->addArea($contentArea);


        $searchArea2 = new Area();
        $searchArea2->setLabel('Footer');
        $searchArea2->setAreaId('footer');
        $searchArea2->addBlock(array('nodeId' => 0, 'blockId' => 4));

        $search = new Node();
        $search->setNodeId('fixture_search');
        $search->setMaxAge(1000);
        $search->setNodeType(NodeInterface::TYPE_DEFAULT);
        $search->setName('Fixture Search');
        $search->setSiteId('1');
        $search->setParentId(NodeInterface::ROOT_NODE_ID);
        $search->setPath('-');
        $search->setOrder(6);
        $search->setRoutePattern('/search');
        $search->setVersion(1);
        $search->setLanguage('fr');
        $search->setStatus($this->getReference('status-published'));
        $search->setDeleted(false);
        $search->setTemplateId('template_home');
        $search->setTheme('fromApp');
        $search->setInFooter(true);
        $search->setInMenu(true);
        $search->addArea($searchArea0);
        $search->addArea($searchArea1);
        $search->addArea($searchArea2);
        $search->addBlock($searchBlock0);
        $search->addBlock($searchBlock1);
        $search->addBlock($searchBlock2);
        $search->addBlock($searchBlock3);
        $search->addBlock($searchBlock4);

        return $search;
    }
}

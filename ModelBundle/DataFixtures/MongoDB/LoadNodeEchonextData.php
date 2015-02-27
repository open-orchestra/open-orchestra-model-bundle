<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use OpenOrchestra\ModelBundle\Document\Area;
use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;

/**
 * Class LoadNodeEchonextData
 */
class LoadNodeEchonextData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadByLanguages('fr', $manager);
        $this->loadByLanguages('en', $manager);

        $manager->flush();
    }

    /**
     * @param string        $languages
     * @param ObjectManager $manager
     */
    protected function loadByLanguages($languages, ObjectManager $manager)
    {
        $nodeTransverse = $this->generateNodeTransverse($languages);
        $manager->persist($nodeTransverse);

        $nodeHome = $this->generateNodeHome($languages);
        $manager->persist($nodeHome);
        $this->addAreaRef($nodeTransverse, $nodeHome);

        $nodeEspaceBDDF = $this->generateEspaceBDDF($languages);
        $manager->persist($nodeEspaceBDDF);
        $this->addAreaRef($nodeTransverse, $nodeEspaceBDDF);

        $nodeEspaceCardifFr = $this->generateEspaceCardif($languages);
        $manager->persist($nodeEspaceCardifFr);
        $this->addAreaRef($nodeTransverse, $nodeEspaceCardifFr);

        $nodeEspaceArval = $this->generateEspaceArval($languages);
        $manager->persist($nodeEspaceArval);
        $this->addAreaRef($nodeTransverse, $nodeEspaceArval);

        $nodeEspaceXXX = $this->generateEspaceXXX($languages);
        $manager->persist($nodeEspaceXXX);
        $this->addAreaRef($nodeTransverse, $nodeEspaceXXX);

        $nodeCardifBienvenue = $this->generateCardifBienvenu($languages);
        $manager->persist($nodeCardifBienvenue);
        $this->addAreaRef($nodeTransverse, $nodeCardifBienvenue);

        $nodeCardifActualite = $this->generateCardifActualite($languages);
        $manager->persist($nodeCardifActualite);
        $this->addAreaRef($nodeTransverse, $nodeCardifActualite);

        $nodeCardifMission = $this->generateCardifMissions($languages);
        $manager->persist($nodeCardifMission);
        $this->addAreaRef($nodeTransverse, $nodeCardifMission);

        $nodeCardifRemun = $this->generateCardifRemun($languages);
        $manager->persist($nodeCardifRemun);
        $this->addAreaRef($nodeTransverse, $nodeCardifRemun);

        $nodeNews = $this->generateNodeNews($languages);
        $manager->persist($nodeNews);
        $this->addAreaRef($nodeTransverse, $nodeNews);

        $manager->persist($nodeTransverse);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 62;
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
     * Generate a node
     *
     * @param array $params
     *
     * @return Node
     */
    protected function generateNode($params)
    {
        $node = new Node();
        $node->setNodeId($params['nodeId']);
        $node->setNodeType('page');
        $node->setSiteId('3');
        $node->setParentId($params['parentId']);
        $node->setPath($params['path']);
        $node->setName($params['name']);
        $node->setRoutePattern($params['routePattern']);
        $node->setVersion(1);
        $node->setLanguage($params['language']);
        $node->setStatus($this->getReference('status-published'));
        $node->setDeleted(false);
        $node->setTemplateId('template_main');
        $node->setTheme('echonext');
        $node->setInMenu($params['inMenu']);
        $node->setInFooter($params['inFooter']);
        if (array_key_exists('order', $params)) {
            $node->setOrder($params['order']);
        }

        return $node;
    }

    /**
     * Generate a specific block
     *
     * @param string $blockType
     * @param string $blockLabel
     * @param int    $nodeId
     * @param string $areaId
     * @param string $id
     * @param string $class
     *
     * @return Block
     */
    protected function generateBlock($blockType, $blockLabel, $nodeId, $areaId, $id = null, $class = null)
    {
        $block = new Block();
        $block->setLabel($blockLabel);
        $block->setComponent($blockType);
        $block->setId($id);
        $block->setClass($class);
        $block->addArea(array('nodeId' => $nodeId, 'areaId' => $areaId));

        return $block;
    }

    /**
     * Generate language block
     *
     * @param $areaId
     * @param int $nodeId
     *
     * @return Block
     */
    protected function generateBlockLang($areaId, $nodeId = 0)
    {
        $blockLang = $this->generateBlock('language_list', 'Language', $nodeId, $areaId, 'lang', 'lang');

        return $blockLang;
    }

    /**
     * Generate a login block
     *
     * @param string $blockLabel
     * @param string $areaId
     * @param int    $nodeId
     *
     * @return Block
     */
    protected function generateBlockLogin($blockLabel, $areaId, $nodeId = 0)
    {
        $blockLogin = $this->generateBlock('login', $blockLabel, $nodeId, $areaId);

        return $blockLogin;
    }

    /**
     * Generate Menu Block
     *
     * @param $blockLabel
     * @param $areaId
     * @param int $nodeId
     *
     * @return Block
     */
    protected function generateBlockMenu($blockLabel, $areaId, $nodeId = 0)
    {
        $menuBlock = $this->generateBlock('menu', $blockLabel, $nodeId, $areaId, 'menu', 'menu');

        return $menuBlock;
    }

    /**
     * Generate a Carrousel
     *
     * @param $blockLabel
     * @param $areaId
     * @param int $nodeId
     *
     * @return Block
     */
    protected function generateBlockCarrousel($carouselId, $blockLabel, $areaId, $nodeId = 0)
    {
        $carrouselBlock = $this->generateBlock('carrousel', $blockLabel, $nodeId, $areaId);
        $carrouselBlock->setAttributes(array(
            'pictures' => array(
                array('src' => "/bundles/fakeapptheme/themes/echonext/img/carroussel/01.jpg"),
                array('src' => "/bundles/fakeapptheme/themes/echonext/img/carroussel/02.jpg"),
                array('src' => "/bundles/fakeapptheme/themes/echonext/img/carroussel/03.jpg"),
                array('src' => "/bundles/fakeapptheme/themes/echonext/img/carroussel/04.jpg"),
            ),
            'width' => "978px",
            'height' => "300px",
            'carrousel_id' => $carouselId,
        ));

        return $carrouselBlock;
    }

    /**
     * Generate a Wysiwyg block
     *
     * @param string $blockLabel
     * @param string $htmlContent
     * @param string $areaId
     * @param int    $nodeId
     *
     * @return Block
     */
    protected function generateBlockWysiwyg($blockLabel, $htmlContent, $areaId, $nodeId = 0)
    {
        $wysiwygBlock = $this->generateBlock('tiny_mce_wysiwyg', $blockLabel, $nodeId, $areaId);
        $wysiwygBlock->setAttributes(array('htmlContent' => $htmlContent));

        return $wysiwygBlock;
    }

    /**
     * Generate a list of Content
     *
     * @param string $class
     * @param string $newsNodeId
     * @param string $blockLabel
     * @param string $areaId
     * @param string $nodeId
     * @param int    $nbCharacters
     *
     * @return Block
     */
    protected function generateBlockContentList($class, $newsNodeId, $blockLabel, $areaId, $nodeId, $nbCharacters = 0, $contentType = 'news')
    {
        $contentList = $this->generateBlock(DisplayBlockInterface::CONTENT_LIST, $blockLabel, $nodeId, $areaId, 'contentNewsList', $class);
        $contentList->setAttributes(array(
            'contentType' => $contentType,
            'contentNodeId' => $newsNodeId,
            'characterNumber' => $nbCharacters,
            'keywords' => null,
            'choiceType' => ContentRepositoryInterface::CHOICE_OR,
        ));

        return $contentList;
    }

    /**
     * Generate a content
     *
     * @param string $divClass
     * @param string $blockLabel
     * @param string $areaId
     * @param int    $nodeId
     *
     * @return Block
     */
    protected function generateBlockContent($divClass, $blockLabel, $areaId, $nodeId = 0)
    {
        $contentBlock = $this->generateBlock('content', $blockLabel, $nodeId, $areaId, 'contentNews', $divClass);

        return $contentBlock;
    }

    /**
     * Generate a sub menu
     *
     * @param string $class
     * @param string $idmenu
     * @param string $nbLevel
     * @param string $node
     * @param string $blockLabel
     * @param string $areaId
     * @param int    $nodeId
     *
     * @return Block
     */
    protected function generateBlockSubMenu($class, $idmenu, $nbLevel, $node, $blockLabel, $areaId, $nodeId = 0)
    {
        $subMenuBlock = $this->generateBlock('sub_menu', $blockLabel, $nodeId, $areaId, $idmenu, $class);
        $subMenuBlock->setAttributes(array(
            'nbLevel' => $nbLevel,
            'node' => $node,
        ));

        return $subMenuBlock;
    }

    /**
     * Generate an Area
     *
     * @param string $areaLabel
     * @param string $areaId
     * @param array  $blocks
     *
     * @return Area
     */
    protected function generateArea($areaLabel, $areaId, $blocks)
    {
        $area = new Area();
        $area->setLabel($areaLabel);
        $area->setAreaId($areaId);
        $area->setBlocks($blocks);

        return $area;
    }

    /**
     * Generate Footer Block
     *
     * @param $blockLabel
     * @param $areaId
     * @param int $nodeId
     *
     * @return Block
     */
    protected function generateFooterBlock($blockLabel, $areaId, $nodeId = 0)
    {
        $footerBlock = $this->generateBlock('footer', $blockLabel, $nodeId, $areaId, 'footer_content', 'footer');

        return $footerBlock;
    }

    /**
     * @return Node
     */
    protected function generateNodeTransverse($language)
    {
        // Header
        $languageBlock = $this->generateBlockLang('main');
        $search = $this->generateBlockWysiwyg('Search', "<div class=search><input type='text'><button type='submit'>Rechercher</button></div>", 'main');
        $logoBlock = $this->generateBlockWysiwyg('Logo', "<a href='#' id='myLogo'> <img src='http://media.openorchestra.inte/echonext-head_logo.png' /> </a><img src='http://media.openorchestra.inte/echonext-head_img.jpg' class='bg-header'/>", 'main');
        $loginBlock = $this->generateBlockLogin('Login', 'main');
        $menuBlock = $this->generateBlockMenu('Menu', 'main');
        $footerBlock = $this->generateFooterBlock('Footer', 'main');

        $mainArea = $this->generateArea('main', 'main',
            array(
                array('nodeId' => 0, 'blockId' => 0),
                array('nodeId' => 0, 'blockId' => 1),
                array('nodeId' => 0, 'blockId' => 2),
                array('nodeId' => 0, 'blockId' => 3),
                array('nodeId' => 0, 'blockId' => 4),
                array('nodeId' => 0, 'blockId' => 5),
            )
        );

        // Generation of the home node
        $node = $this->generateNode(array(
            'nodeId' => NodeInterface::TRANSVERSE_NODE_ID,
            'parentId' => '-',
            'path' => '-',
            'name' => NodeInterface::TRANSVERSE_NODE_ID,
            'inMenu' => false,
            'inFooter' => false,
            'language' => $language,
            'routePattern' => '/' . NodeInterface::TRANSVERSE_NODE_ID,
        ));
        $node->setNodeType(NodeInterface::TYPE_GENERAL);

        $node->addArea($mainArea);
        $node->addBlock($languageBlock);
        $node->addBlock($loginBlock);
        $node->addBlock($logoBlock);
        $node->addBlock($search);
        $node->addBlock($menuBlock);
        $node->addBlock($footerBlock);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateNodeHome($language)
    {
        // Header
        $headerArea = $this->generateArea('Header', 'header',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4),
            )
        );
        $headerArea->setBoDirection('v');

        // Main
        $descBlock = $this->generateBlockWysiwyg('Home', '<h1>Bienvenue sur le site de demo Echonext.</h1>', 'main');
        $carrouselBlock = $this->generateBlockCarrousel('slider1_container', 'Carrousel', 'main');
        $newsList = $this->generateBlockContentList('content-list', 'news', 'News 6', 'main', 0, 70);

        $mainArea = $this->generateArea('Main', 'main',
            array(
                array('nodeId' => 0, 'blockId' => 0),
                array('nodeId' => 0, 'blockId' => 1),
                array('nodeId' => 0, 'blockId' => 2),
            )
        );

        $footerArea = $this->generateArea('Footer', 'footer',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 5),
            )
        );

        // Generation of the home node
        $node = $this->generateNode(array(
            'nodeId' => NodeInterface::ROOT_NODE_ID,
            'parentId' => '-',
            'path' => '-',
            'name' => 'Home',
            'url' => 'home',
            'inMenu' => true,
            'inFooter' => true,
            'language' => $language,
            'routePattern' => '',
        ));

        $node->addArea($headerArea);

        $node->addArea($mainArea);
        $node->addBlock($descBlock);
        $node->addBlock($carrouselBlock);
        $node->addBlock($newsList);

        $node->addArea($footerArea);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateNodeNews($language)
    {
        // Header
        $headerArea = $this->generateArea('Header', 'header',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4),
            )
        );
        $headerArea->setBoDirection('v');

        // Main
        $newsList = $this->generateBlockContent('news', 'News', 'main');

        $mainArea = $this->generateArea('Main', 'main',
            array(
                array('nodeId' => 0, 'blockId' => 0),
            )
        );

        // Footer
        $footerArea = $this->generateArea('Footer', 'footer',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 5),
            )
        );

        $node = $this->generateNode(array(
            'nodeId' => 'news',
            'parentId' => NodeInterface::ROOT_NODE_ID,
            'path' => 'news',
            'name' => 'News',
            'url' => 'news',
            'inMenu' => false,
            'inFooter' => false,
            'language' => $language,
            'routePattern' => '/news/{newsId}',
        ));

        $node->addArea($headerArea);
        $node->addArea($mainArea);
        $node->addArea($footerArea);

        $node->addBlock($newsList);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateEspaceBDDF($language)
    {
        // Header
        $headerArea = $this->generateArea('Header', 'header',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4),
            )
        );
        $headerArea->setBoDirection('v');

        // Main
        $titleBlock = $this->generateBlockWysiwyg('BDDF', '<h1>Page Espace BDDF</h1>', 'main');
        $contentListBlock = $this->generateBlockContentList('', 'news', 'car block', 'main', 0, 70, 'car');
        $contentListBlock->addAttribute('contentTemplate', '<div>On affiche le contenu {{ content.name }} avec un template de la base</div>');

        $mainArea = $this->generateArea('Main', 'main',
            array(
                array('nodeId' => 0, 'blockId' => 0),
                array('nodeId' => 0, 'blockId' => 1),
            )
        );

        // Footer
        $footerArea = $this->generateArea('Footer', 'footer',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 5),
            )
        );

        $node = $this->generateNode(array(
            'nodeId' => 'espace_bddf',
            'parentId' => NodeInterface::ROOT_NODE_ID,
            'path' => 'espace-bddf',
            'name' => 'Espace BDDF',
            'url' => 'espace-bddf',
            'inMenu' => true,
            'inFooter' => true,
            'language' => $language,
            'routePattern' => 'espace-bddf',
        ));

        $node->addArea($headerArea);
        $node->addArea($mainArea);
        $node->addArea($footerArea);

        $node->addBlock($titleBlock);
        $node->addBlock($contentListBlock);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateEspaceCardif($language)
    {
        // Header
        $headerArea = $this->generateArea('Header', 'header',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4),
            )
        );
        $headerArea->setBoDirection('v');

        // Main
        $subMenu = $this->generateBlockSubMenu('left_menu', 'cardif_left_menu', 2, 'espace_Cardif', 'Sub Menu', 'left_menu');
        $titleBlock = $this->generateBlockWysiwyg('Cardif', "<h1>Bienvenue sur l'espace de cardif</h1>", 'main');
        $bodyBlock = $this->generateBlockWysiwyg('Body cardif', '<div class="body-espace-cardif"><p>BNP Paribas cardif est l\'un des François Villeroy de Galhau,
            Directeur Général Délégué de BNP Paribas répond à nos questions. Cras non dui id neque mattis molestie. Quisque feugiat metus in est aliquet, nec convallis
            ante blandit. Suspendisse tincidunt tortor et tellus eleifend bibendum. Fusce fringilla mauris dolor, quis tempus diam tempus eu. Morbi enim orci, aliquam at
            sapien eu, dignissim commodo enim. Nulla ultricies erat non facilisis feugiat. Quisque fringilla ante lacus, vitae viverra magna aliquam non. Pellentesque
            quis diam suscipit, tincidunt felis eget, mollis mauris. Nulla facilisi.</p><p>Nunc tincidunt pellentesque suscipit. Donec tristique massa at turpis fringilla,
            non aliquam ante luctus. Nam in felis tristique, scelerisque magna eget, sagittis purus. Maecenas malesuada placerat rutrum. Vestibulum sem urna, pharetra et
            fermentum a, iaculis quis augue. Ut ac neque mauris. In vel risus dui. Fusce lacinia a velit vitae condimentum.</p></div>', 'main');

        $leftMenu = $this->generateArea('Left menu', 'left_menu', array(
            array('nodeId' => 0, 'blockId' => 0),));
        $mainArea = $this->generateArea('Main', 'main',
            array(
                array('nodeId' => 0, 'blockId' => 1),
                array('nodeId' => 0, 'blockId' => 2),
            )
        );
        $bodyArea = $this->generateArea('Body', 'body', array());
        $bodyArea->setBoDirection('v');
        $bodyArea->addArea($leftMenu);
        $bodyArea->addArea($mainArea);

        // Footer
        $footerArea = $this->generateArea('Footer', 'footer',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 5),
            )
        );

        $node = $this->generateNode(array(
            'nodeId' => 'espace_Cardif',
            'parentId' => NodeInterface::ROOT_NODE_ID,
            'path' => 'espace-cardif',
            'name' => 'Espace Cardif',
            'url' => 'espace-cardif',
            'inMenu' => true,
            'inFooter' => true,
            'language' => $language,
            'routePattern' => 'espace-cardif',
            'order' => 1
        ));

        $node->addArea($headerArea);
        $node->addArea($bodyArea);
        $node->addArea($footerArea);

        $node->addBlock($subMenu);
        $node->addBlock($titleBlock);
        $node->addBlock($bodyBlock);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateEspaceArval($language)
    {
        // Header
        $headerArea = $this->generateArea('Header', 'header',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4),
            )
        );
        $headerArea->setBoDirection('v');

        // Main
        $titleBlock = $this->generateBlockWysiwyg('BDDF', '<h1>Page Espace Arval</h1>', 'main');

        $mainArea = $this->generateArea('Main', 'main',
            array(
                array('nodeId' => 0, 'blockId' => 0),
            )
        );

        // Footer
        $footerArea = $this->generateArea('Footer', 'footer',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 5),
            )
        );

        $node = $this->generateNode(array(
            'nodeId' => 'espace_Arval',
            'parentId' => NodeInterface::ROOT_NODE_ID,
            'path' => 'espace-arval',
            'name' => 'Espace Arval',
            'url' => 'espace-arval',
            'inMenu' => true,
            'inFooter' => true,
            'language' => $language,
            'routePattern' => 'espace-arval',
            'order' => 2,
        ));

        $node->addArea($headerArea);
        $node->addArea($mainArea);
        $node->addArea($footerArea);

        $node->addBlock($titleBlock);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateEspaceXXX($language)
    {
        // Header
        $headerArea = $this->generateArea('Header', 'header',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4),
            )
        );
        $headerArea->setBoDirection('v');

        // Main
        $titleBlock = $this->generateBlockWysiwyg('BDDF', '<h1>Page Espace XXX</h1>', 'main');

        $mainArea = $this->generateArea('Main', 'main',
            array(
                array('nodeId' => 0, 'blockId' => 0),
            )
        );

        // Footer
        $footerArea = $this->generateArea('Footer', 'footer',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 5),
            )
        );

        $node = $this->generateNode(array(
            'nodeId' => 'espace_XXX',
            'parentId' => NodeInterface::ROOT_NODE_ID,
            'path' => 'espace-xxx',
            'name' => 'Espace XXX',
            'url' => 'espace-xxx',
            'inMenu' => true,
            'inFooter' => true,
            'language' => $language,
            'routePattern' => 'espace-xxx',
            'order' => 3
        ));

        $node->addArea($headerArea);
        $node->addArea($mainArea);
        $node->addArea($footerArea);

        $node->addBlock($titleBlock);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateCardifBienvenu($language)
    {
        // Header
        $headerArea = $this->generateArea('Header', 'header',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4),
            )
        );
        $headerArea->setBoDirection('v');

        // Main
        $subMenu = $this->generateBlockSubMenu('left_menu', 'cardif_left_menu', 2, 'espace_Cardif', 'Sub Menu', 'left_menu');
        $titleBlock = $this->generateBlockWysiwyg('BDDF', '<h1>Bienvenue sur l\'espace Cardif</h1>', 'main');
        $bodyBlock = $this->generateBlockWysiwyg('Body cardif', '<div class="body-espace-cardif"><p>BNP Paribas cardif est l\'un des François Villeroy de Galhau,
            Directeur Général Délégué de BNP Paribas répond à nos questions. Cras non dui id neque mattis molestie. Quisque feugiat metus in est aliquet, nec convallis
            ante blandit. Suspendisse tincidunt tortor et tellus eleifend bibendum. Fusce fringilla mauris dolor, quis tempus diam tempus eu. Morbi enim orci, aliquam at
            sapien eu, dignissim commodo enim. Nulla ultricies erat non facilisis feugiat. Quisque fringilla ante lacus, vitae viverra magna aliquam non. Pellentesque
            quis diam suscipit, tincidunt felis eget, mollis mauris. Nulla facilisi.</p><p>Nunc tincidunt pellentesque suscipit. Donec tristique massa at turpis fringilla,
            non aliquam ante luctus. Nam in felis tristique, scelerisque magna eget, sagittis purus. Maecenas malesuada placerat rutrum. Vestibulum sem urna, pharetra et
            fermentum a, iaculis quis augue. Ut ac neque mauris. In vel risus dui. Fusce lacinia a velit vitae condimentum.</p></div>', 'main');

        $leftMenu = $this->generateArea('Left menu', 'left_menu', array(
            array('nodeId' => 0, 'blockId' => 0),));
        $mainArea = $this->generateArea('Main', 'main',
            array(
                array('nodeId' => 0, 'blockId' => 1),
                array('nodeId' => 0, 'blockId' => 2),
            )
        );
        $bodyArea = $this->generateArea('Body', 'body', array());
        $bodyArea->setBoDirection('v');
        $bodyArea->addArea($leftMenu);
        $bodyArea->addArea($mainArea);

        // Footer
        $footerArea = $this->generateArea('Footer', 'footer',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 5),
            )
        );

        $node = $this->generateNode(array(
            'nodeId' => 'cardif_bienvenu',
            'parentId' => 'espace_Cardif',
            'path' => 'bienvenu',
            'name' => 'Bienvenu',
            'url' => 'bienvenu',
            'inMenu' => false,
            'inFooter' => false,
            'language' => $language,
            'routePattern' => 'bienvenu',
        ));

        $node->addArea($headerArea);
        $node->addArea($bodyArea);
        $node->addArea($footerArea);

        $node->addBlock($subMenu);
        $node->addBlock($titleBlock);
        $node->addBlock($bodyBlock);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateCardifActualite($language)
    {
        // Header
        $headerArea = $this->generateArea('Header', 'header',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4),
            )
        );
        $headerArea->setBoDirection('v');

        // Main
        $subMenu = $this->generateBlockSubMenu('left_menu', 'cardif_left_menu', 2, 'espace_Cardif', 'Sub Menu', 'left_menu');
        $titleBlock = $this->generateBlockWysiwyg('BDDF', '<h1>Page actualité Cardif</h1>', 'main');
        $bodyBlock = $this->generateBlockWysiwyg('Body cardif', '<div class="body-espace-cardif"><p>BNP Paribas cardif est l\'un des François Villeroy de Galhau,
            Directeur Général Délégué de BNP Paribas répond à nos questions. Cras non dui id neque mattis molestie. Quisque feugiat metus in est aliquet, nec convallis
            ante blandit. Suspendisse tincidunt tortor et tellus eleifend bibendum. Fusce fringilla mauris dolor, quis tempus diam tempus eu. Morbi enim orci, aliquam at
            sapien eu, dignissim commodo enim. Nulla ultricies erat non facilisis feugiat. Quisque fringilla ante lacus, vitae viverra magna aliquam non. Pellentesque
            quis diam suscipit, tincidunt felis eget, mollis mauris. Nulla facilisi.</p><p>Nunc tincidunt pellentesque suscipit. Donec tristique massa at turpis fringilla,
            non aliquam ante luctus. Nam in felis tristique, scelerisque magna eget, sagittis purus. Maecenas malesuada placerat rutrum. Vestibulum sem urna, pharetra et
            fermentum a, iaculis quis augue. Ut ac neque mauris. In vel risus dui. Fusce lacinia a velit vitae condimentum.</p></div>', 'main');

        $leftMenu = $this->generateArea('Left menu', 'left_menu', array(
            array('nodeId' => 0, 'blockId' => 0),));
        $mainArea = $this->generateArea('Main', 'main',
            array(
                array('nodeId' => 0, 'blockId' => 1),
                array('nodeId' => 0, 'blockId' => 2),
            )
        );
        $bodyArea = $this->generateArea('Body', 'body', array());
        $bodyArea->setBoDirection('v');
        $bodyArea->addArea($leftMenu);
        $bodyArea->addArea($mainArea);

        // Footer
        $footerArea = $this->generateArea('Footer', 'footer',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 5),
            )
        );

        $node = $this->generateNode(array(
            'nodeId' => 'cardif_actualite',
            'parentId' => 'espace_Cardif',
            'path' => 'actualite',
            'name' => 'Actualité',
            'url' => 'actualite',
            'inMenu' => true,
            'inFooter' => true,
            'language' => $language,
            'order' => 1,
            'routePattern' => 'actualite',
        ));

        $node->addArea($headerArea);
        $node->addArea($bodyArea);
        $node->addArea($footerArea);

        $node->addBlock($subMenu);
        $node->addBlock($titleBlock);
        $node->addBlock($bodyBlock);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateCardifMissions($language)
    {
        // Header
        $headerArea = $this->generateArea('Header', 'header',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4),
            )
        );
        $headerArea->setBoDirection('v');

        // Main
        $subMenu = $this->generateBlockSubMenu('left_menu', 'cardif_left_menu', 2, 'espace_Cardif', 'Sub Menu', 'left_menu');
        $titleBlock = $this->generateBlockWysiwyg('BDDF', '<h1>Page Missions Cardif</h1>', 'main');
        $bodyBlock = $this->generateBlockWysiwyg('Body cardif', '<div class="body-espace-cardif"><p>BNP Paribas cardif est l\'un des François Villeroy de Galhau,
            Directeur Général Délégué de BNP Paribas répond à nos questions. Cras non dui id neque mattis molestie. Quisque feugiat metus in est aliquet, nec convallis
            ante blandit. Suspendisse tincidunt tortor et tellus eleifend bibendum. Fusce fringilla mauris dolor, quis tempus diam tempus eu. Morbi enim orci, aliquam at
            sapien eu, dignissim commodo enim. Nulla ultricies erat non facilisis feugiat. Quisque fringilla ante lacus, vitae viverra magna aliquam non. Pellentesque
            quis diam suscipit, tincidunt felis eget, mollis mauris. Nulla facilisi.</p><p>Nunc tincidunt pellentesque suscipit. Donec tristique massa at turpis fringilla,
            non aliquam ante luctus. Nam in felis tristique, scelerisque magna eget, sagittis purus. Maecenas malesuada placerat rutrum. Vestibulum sem urna, pharetra et
            fermentum a, iaculis quis augue. Ut ac neque mauris. In vel risus dui. Fusce lacinia a velit vitae condimentum.</p></div>', 'main');

        $leftMenu = $this->generateArea('Left menu', 'left_menu', array(
            array('nodeId' => 0, 'blockId' => 0),));
        $mainArea = $this->generateArea('Main', 'main',
            array(
                array('nodeId' => 0, 'blockId' => 1),
                array('nodeId' => 0, 'blockId' => 2),
            )
        );
        $bodyArea = $this->generateArea('Body', 'body', array());
        $bodyArea->setBoDirection('v');
        $bodyArea->addArea($leftMenu);
        $bodyArea->addArea($mainArea);

        // Footer
        $footerArea = $this->generateArea('Footer', 'footer',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 5),
            )
        );

        $node = $this->generateNode(array(
            'nodeId' => 'cardif_missions',
            'parentId' => 'espace_Cardif',
            'path' => 'missions',
            'name' => 'Mission',
            'url' => 'missions',
            'inMenu' => true,
            'inFooter' => true,
            'language' => $language,
            'routePattern' => 'meissions',
            'order' => 2,
        ));

        $node->addArea($headerArea);
        $node->addArea($bodyArea);
        $node->addArea($footerArea);

        $node->addBlock($subMenu);
        $node->addBlock($titleBlock);
        $node->addBlock($bodyBlock);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateCardifRemun($language)
    {
        // Header
        $headerArea = $this->generateArea('Header', 'header',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 0),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 1),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 2),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 3),
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4),
            )
        );
        $headerArea->setBoDirection('v');

        // Main
        $subMenu = $this->generateBlockSubMenu('left_menu', 'cardif_left_menu', 2, 'espace_Cardif', 'Sub Menu', 'left_menu');
        $titleBlock = $this->generateBlockWysiwyg('BDDF', '<h1>Page Cardif Rémunération</h1>', 'main');
        $bodyBlock = $this->generateBlockWysiwyg('Body cardif', '<div class="body-espace-cardif"><p>BNP Paribas cardif est l\'un des François Villeroy de Galhau,
            Directeur Général Délégué de BNP Paribas répond à nos questions. Cras non dui id neque mattis molestie. Quisque feugiat metus in est aliquet, nec convallis
            ante blandit. Suspendisse tincidunt tortor et tellus eleifend bibendum. Fusce fringilla mauris dolor, quis tempus diam tempus eu. Morbi enim orci, aliquam at
            sapien eu, dignissim commodo enim. Nulla ultricies erat non facilisis feugiat. Quisque fringilla ante lacus, vitae viverra magna aliquam non. Pellentesque
            quis diam suscipit, tincidunt felis eget, mollis mauris. Nulla facilisi.</p><p>Nunc tincidunt pellentesque suscipit. Donec tristique massa at turpis fringilla,
            non aliquam ante luctus. Nam in felis tristique, scelerisque magna eget, sagittis purus. Maecenas malesuada placerat rutrum. Vestibulum sem urna, pharetra et
            fermentum a, iaculis quis augue. Ut ac neque mauris. In vel risus dui. Fusce lacinia a velit vitae condimentum.</p></div>', 'main');

        $leftMenu = $this->generateArea('Left menu', 'left_menu', array(
            array('nodeId' => 0, 'blockId' => 0),));
        $mainArea = $this->generateArea('Main', 'main',
            array(
                array('nodeId' => 0, 'blockId' => 1),
                array('nodeId' => 0, 'blockId' => 2),
            )
        );
        $bodyArea = $this->generateArea('Body', 'body', array());
        $bodyArea->setBoDirection('v');
        $bodyArea->addArea($leftMenu);
        $bodyArea->addArea($mainArea);

        // Footer
        $footerArea = $this->generateArea('Footer', 'footer',
            array(
                array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 5),
            )
        );

        $node = $this->generateNode(array(
            'nodeId' => 'cardif_remunerations',
            'parentId' => 'espace_Cardif',
            'path' => 'remunarations-variables',
            'name' => 'Remunerations',
            'url' => 'remunarations-variables',
            'inMenu' => true,
            'inFooter' => true,
            'language' => $language,
            'order' => 3,
            'routePattern' => 'remunerations-variables',
        ));
        $node->setScheme(SchemeableInterface::SCHEME_HTTPS);

        $node->addArea($headerArea);
        $node->addArea($bodyArea);
        $node->addArea($footerArea);

        $node->addBlock($subMenu);
        $node->addBlock($titleBlock);
        $node->addBlock($bodyBlock);

        return $node;
    }
}

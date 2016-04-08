<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelBundle\Document\Area;

/**
 * Class LoadRootNodeDemoData
 */
class LoadRootNodeDemoData extends AbstractFixture implements OrderedFixtureInterface, OrchestraProductionFixturesInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $references = array();
        $references["status-published"] = $this->getReference('status-published');
        $references["status-draft"] = $this->getReference('status-draft');
        $references["siteId"] = $this->getReference("site2")->getSiteId();
        $references["templateId"] = $this->getReference("homepage-template")->getTemplateId();
        $references["template-areas"] = $this->getReference("homepage-template")->getAreas();

        $routePattern = "/";
        $languages = array("fr", "en", "de");

        foreach ($languages as $language) {
            $transverse = $this->generateNodeTransverseGlobal($references, $language);
            $manager->persist($transverse);
        }

        foreach ($languages as $language) {
            $node = $this->generateNodeGlobal($references, $language, $routePattern);
            $manager->persist($node);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 560;
    }

    /**
     * @param array $references
     * @param string $language
     * @param string $routePattern
     *
     * @return Node
     */
    protected function generateNodeGlobal($references, $language, $routePattern)
    {
        $siteHome = $this->createBaseNode($references);
        $siteHome->setLanguage($language);
        $siteHome->setNodeId(NodeInterface::ROOT_NODE_ID);
        $siteHome->setName('Homepage');
        $siteHome->setCreatedBy('fake_admin');
        $siteHome->setParentId('-');
        $siteHome->setOrder(0);
        $siteHome->setRoutePattern($routePattern);
        $siteHome->setInFooter(false);
        $siteHome->setInMenu(true);
        $siteHome->setSitemapChangefreq('hourly');
        $siteHome->setSitemapPriority('0.8');

        return $siteHome;
    }

    /**
     * @param array $references
     * @return Node
     */
    protected function createBaseNode($references)
    {
        $node = new Node();
        $node->setMaxAge(1000);
        $node->setNodeType(NodeInterface::TYPE_DEFAULT);
        $node->setSiteId($references["siteId"]);
        $node->setPath('-');
        $node->setVersion("1");
        $node->setStatus($references["status-published"]);
        if ('status-published' == $references["status-published"]) {
            $node->setCurrentlyPublished(true);
        }
        $node->setDeleted(false);
        $node->setTemplateId($references["templateId"]);
        $node->setAreas($references["template-areas"]);
        $node->setTheme('themePresentation');
        $node->setDefaultSiteTheme(true);
        $node->setBoDirection('v');

        return $node;
    }

    /**
     * @param array  $references
     * @param string $language
     *
     * @return Node
     */
    protected function generateNodeTransverseGlobal($references, $language)
    {
        $mainArea = $this->createArea('main','main','main');

        $nodeTransverse = new Node();
        $nodeTransverse->setNodeId(NodeInterface::TRANSVERSE_NODE_ID);
        $nodeTransverse->setMaxAge(1000);
        $nodeTransverse->setNodeType(NodeInterface::TYPE_TRANSVERSE);
        $nodeTransverse->setName(NodeInterface::TRANSVERSE_NODE_ID);
        $nodeTransverse->setSiteId('2');
        $nodeTransverse->setParentId('-');
        $nodeTransverse->setPath('-');
        $nodeTransverse->setVersion(1);
        $nodeTransverse->setOrder(1);
        $nodeTransverse->setLanguage($language);
        $nodeTransverse->setStatus($references["status-draft"]);
        $nodeTransverse->setDeleted(false);
        $nodeTransverse->setTemplateId('');
        $nodeTransverse->setTheme('');
        $nodeTransverse->setInFooter(false);
        $nodeTransverse->setInMenu(false);
        $nodeTransverse->addArea($mainArea);

        return $nodeTransverse;
    }

    /**
     * @param string $label
     * @param string $areaId
     * @param string $htmlClass
     * @param string $boDirection
     *
     * @return Area
     */
    protected function createArea($label, $areaId, $htmlClass = null, $boDirection = 'v')
    {
        $area = new Area();
        $area->setLabel($label);
        $area->setAreaId($areaId);
        $area->setBoDirection($boDirection);
        if ($htmlClass !== null) {
            $area->setHtmlClass($htmlClass);
        }

        return $area;
    }
}
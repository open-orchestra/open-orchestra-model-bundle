<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class HomeDataGenerator
 */
class HomeDataGenerator extends AbstractDataGenerator
{
    protected $homeNode;
    /**
     * @return Node
     */
    protected function generateNodeFr()
    {
        $routePattern = "/";
        $language = "fr";

        return $this->generateNodeGlobal($language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeEn()
    {
        $routePattern = "en";
        $language = "en";

        return $this->generateNodeGlobal($language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeDe()
    {
        $routePattern = "de";
        $language = "de";

        return $this->generateNodeGlobal($language, $routePattern);
    }

    /**
     * @param string $language
     * @param string $routePattern
     *
     * @return Node
     */
    protected function generateNodeGlobal($language, $routePattern)
    {
        $siteHomeArea0 = $this->createArea('Header','header','header','h');
        
        $siteHome = $this->createBaseNode();
        $siteHome->setLanguage($language);
        $siteHome->setNodeId(NodeInterface::ROOT_NODE_ID);
        $siteHome->setName('Orchestra ?');
        $siteHome->setCreatedBy('fake_admin');
        $siteHome->setParentId('-');
        $siteHome->setOrder(0);
        $siteHome->setRoutePattern($routePattern);
        $siteHome->setInFooter(false);
        $siteHome->setInMenu(true);
        $siteHome->addArea($siteHomeArea0);
        $siteHome->setSitemapChangefreq('hourly');
        $siteHome->setSitemapPriority('0.8');

        return $siteHome;
    }
}

<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class HomeDataGenerator
 */
class HomeDataGenerator extends AbstractDataGenerator
{
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
        $nodeHomeArea0 = $this->createArea('Header','header','header','h');
        
        $nodeHome = $this->createBaseNode();
        $nodeHome->setLanguage($language);
        $nodeHome->setNodeId(NodeInterface::ROOT_NODE_ID);
        $nodeHome->setName('Orchestra ?');
        $nodeHome->setCreatedBy('fake_admin');
        $nodeHome->setParentId('-');
        $nodeHome->setOrder(0);
        $nodeHome->setRoutePattern($routePattern);
        $nodeHome->setInFooter(false);
        $nodeHome->setInMenu(true);
        $nodeHome->addArea($nodeHomeArea0);
        $nodeHome->setSitemapChangefreq('hourly');
        $nodeHome->setSitemapPriority('0.8');

        return $nodeHome;
    }
}

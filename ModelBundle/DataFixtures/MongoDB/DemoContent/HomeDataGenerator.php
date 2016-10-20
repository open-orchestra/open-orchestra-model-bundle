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
        $routePattern = "/";
        $language = "en";

        return $this->generateNodeGlobal($language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeDe()
    {
        $routePattern = "/";
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
        $nodeHome = $this->createBaseNode();
        $nodeHome->setLanguage($language);
        $nodeHome->setNodeId(NodeInterface::ROOT_NODE_ID);
        $nodeHome->setName('Homepage');
        $nodeHome->setBoLabel('Homepage');
        $nodeHome->setCreatedBy('fake_admin');
        $nodeHome->setParentId('-');
        $nodeHome->setOrder(0);
        $nodeHome->setRoutePattern($routePattern);
        $nodeHome->setInFooter(false);
        $nodeHome->setInMenu(true);
        $nodeHome->setSitemapChangefreq('hourly');
        $nodeHome->setSitemapPriority('0.8');

        return $nodeHome;
    }
}

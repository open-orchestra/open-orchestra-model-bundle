<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelBundle\Document\RouteDocument;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadDeletedSiteNodeData
 */
class LoadDeletedSiteNodeData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $rootNodeFr = $this->generateRootNodeFr();
        $manager->persist($rootNodeFr);
        $this->generateRouteNode($manager, $rootNodeFr);

        $deletedNodeFr = $this->generateDeletedNodeFr();
        $manager->persist($deletedNodeFr);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 561;
    }

    /**
     * @param ObjectManager $manager
     * @param NodeInterface $node
     */
    protected function generateRouteNode(ObjectManager $manager, $node)
    {
        $site = $this->getReference('site3');

        foreach ($site->getAliases() as $key => $alias) {
            if ($alias->getLanguage() == $node->getLanguage()) {
                $route = new RouteDocument();
                $route->setName($key . '_' . $node->getId());
                $route->setHost($alias->getDomain());
                $scheme = $alias->getScheme();
                $route->setSchemes($scheme);
                $route->setLanguage($node->getLanguage());
                $route->setNodeId($node->getNodeId());
                $route->setSiteId($site->getSiteId());
                $route->setAliasId($key);

                $route->setPattern($node->getRoutePattern());
                $manager->persist($route);
            }
        }

    }

    /**
     * @return Node
     */
    protected function generateRootNodeFr()
    {
        $node = $this->getSitePageNode();
        $node->setNodeId(NodeInterface::ROOT_NODE_ID);
        $node->setStatus($this->getReference('status-draft'));
        $node->setDeleted(false);

        $node->setName("Site fermé");
        $node->setRoutePattern("site-ferme");
        $node->setLanguage("fr");
        $node->setCreatedBy('fake_admin');
        $node->setParentId('-');
        $node->setOrder(0);

        return $node;
    }

    /**
     * @return Node
     */
    protected function generateDeletedNodeFr()
    {
        $node = $this->getSitePageNode();
        $node->setNodeId('some-deleted-node-id');
        $node->setStatus($this->getReference('status-draft'));
        $node->setDeleted(true);

        $node->setName("Page supprimée");
        $node->setRoutePattern("page-supprimee");
        $node->setLanguage("fr");
        $node->setCreatedBy('fake_admin');
        $node->setParentId(NodeInterface::ROOT_NODE_ID);
        $node->setOrder(0);

        return $node;
    }

    /**
     * @return Node
     */
    protected function getSitePageNode()
    {
        $node = new Node();
        $node->setMaxAge(1000);
        $node->setNodeType('page');
        $node->setSiteId('3');
        $node->setPath('-');
        $node->setVersion('1');
        $node->setTemplate('home');

        return $node;
    }
}

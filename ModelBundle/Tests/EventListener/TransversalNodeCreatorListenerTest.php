<?php

namespace OpenOrchestra\ModelBundle\Tests\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use OpenOrchestra\ModelBundle\EventListener\TransversalNodeCreatorListener;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class TransversalNodeCreatorListenerTest
 */
class TransversalNodeCreatorListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TransversalNodeCreatorListener
     */
    protected $listener;

    protected $documentManager;
    protected $nodeRepository;
    protected $nodeManager;
    protected $container;
    protected $newNode;
    protected $nodeFr;
    protected $nodeEn;
    protected $siteId;
    protected $nodes;
    protected $event;
    protected $site;

    public function setUp()
    {
        $this->siteId = '1';
        $this->nodes = new ArrayCollection();

        $this->newNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $this->nodeFr = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->nodeFr)->getLanguage()->thenReturn('fr');
        $this->nodeEn = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->nodeEn)->getLanguage()->thenReturn('en');

        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\NodeRepository');
        Phake::when($this->nodeRepository)
            ->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'fr', $this->siteId)
            ->thenReturn($this->nodeFr);
        Phake::when($this->nodeRepository)
            ->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'en', $this->siteId)
            ->thenReturn($this->nodeEn);
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        Phake::when($this->container)->get(Phake::anyParameters())->thenReturn($this->nodeRepository);

        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->site)->getSiteId()->thenReturn($this->siteId);
        $this->nodeManager = Phake::mock('OpenOrchestra\ModelBundle\Manager\NodeManager');
        Phake::when($this->nodeManager)->createTransverseNode(Phake::anyParameters())->thenReturn($this->newNode);
        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $this->event = Phake::mock('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs');
        Phake::when($this->event)->getDocument()->thenReturn($this->site);
        Phake::when($this->event)->getDocumentManager()->thenReturn($this->documentManager);

        $this->listener = new TransversalNodeCreatorListener($this->nodeManager);
        $this->listener->setContainer($this->container);
    }

    /**
     * test if the method is callable
     */
    public function testMethodPrePersistCallable()
    {
        $this->assertTrue(method_exists($this->listener, 'prePersist'));
    }

    /**
     * test if the method is callable
     */
    public function testMethodPreUpdateCallable()
    {
        $this->assertTrue(method_exists($this->listener, 'preUpdate'));
    }

    /**
     * test if the method is callable
     */
    public function testMethodPostFlushCallable()
    {
        $this->assertTrue(method_exists($this->listener, 'postFlush'));
    }

    /**
     * Test with node already existing
     *
     * @param string $method
     *
     * @dataProvider provideMethod
     */
    public function testWithAlreadyExistingNodes($method)
    {
        Phake::when($this->site)->getLanguages()->thenReturn(array('fr', 'en'));

        $this->listener->$method($this->event);

        Phake::verify($this->nodeRepository)->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'fr', $this->siteId);
        Phake::verify($this->nodeRepository)->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'en', $this->siteId);
        $this->assertEmpty($this->listener->nodes);
    }

    /**
     * Test with node non existing
     *
     * @param string $method
     *
     * @dataProvider provideMethod
     */
    public function testWithNonExistingNodes($method)
    {
        Phake::when($this->site)->getLanguages()->thenReturn(array('fr', 'en'));
        Phake::when($this->nodeRepository)->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(Phake::anyParameters())->thenReturn(null);

        $this->listener->$method($this->event);

        Phake::verify($this->nodeRepository)->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'fr', $this->siteId);
        Phake::verify($this->nodeRepository)->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'en', $this->siteId);
        Phake::verify($this->nodeManager)->createTransverseNode('fr', $this->siteId);
        $this->assertCount(2, $this->listener->nodes);
        $this->assertSame($this->newNode, $this->listener->nodes[0]);
        $this->assertSame($this->newNode, $this->listener->nodes[1]);
    }

    /**
     * @return array
     */
    public function provideMethod()
    {
        return array(
            array('prePersist'),
            array('preUpdate'),
        );
    }

    /**
     * @param array $nodes
     *
     * @dataProvider provideNodes
     */
    public function testPostFlush($nodes)
    {
        $event = Phake::mock('Doctrine\ODM\MongoDB\Event\PostFlushEventArgs');
        Phake::when($event)->getDocumentManager()->thenReturn($this->documentManager);
        $this->listener->nodes = $nodes;

        $this->listener->postFlush($event);

        foreach ($nodes as $node) {
            Phake::verify($this->documentManager, Phake::atLeast(1))->persist($node);
        }
        Phake::verify($this->documentManager)->flush();
        $this->assertEmpty($this->listener->nodes);
    }

    /**
     * @return array
     */
    public function provideNodes()
    {
        $node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $node2 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $node3 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        return array(
            array($node1),
            array($node1, $node2),
            array($node1, $node2, $node3),
        );
    }

    /**
     * Test with no nodes
     */
    public function testPostFlushWithEmptyNodes()
    {
        $event = Phake::mock('Doctrine\ODM\MongoDB\Event\PostFlushEventArgs');
        Phake::when($event)->getDocumentManager()->thenReturn($this->documentManager);

        $this->listener->postFlush($event);

        Phake::verify($this->documentManager, Phake::never())->flush();
        $this->assertEmpty($this->listener->nodes);
    }
}

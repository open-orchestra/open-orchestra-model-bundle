<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventListener;

use Phake;
use OpenOrchestra\ModelBundle\EventListener\GeneratePathListener;
use OpenOrchestra\ModelBundle\Document\Node;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class GeneratePathListenerTest
 */
class GeneratePathListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;
    protected $container;
    protected $nodeRepository;
    protected $lifecycleEventArgs;
    protected $documentManager;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->lifecycleEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs');

        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\NodeRepository');
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        Phake::when($this->container)->get(Phake::anyParameters())->thenReturn($this->nodeRepository);
        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');

        $this->listener = new GeneratePathListener($this->container);
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
     *
     * @param string          $method
     * @param Node            $node
     * @param Node            $parentNode
     * @param ArrayCollection $childs
     * @param array           $expectedPath
     *
     * @dataProvider provideNodeForRecord
     */
    public function testRecord($method, Node $node, Node $parentNode, ArrayCollection $childs, $expectedPath)
    {
        $documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $unitOfWork = Phake::mock('Doctrine\ODM\MongoDB\UnitOfWork');

        Phake::when($this->nodeRepository)->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(Phake::anyParameters())->thenReturn($parentNode);
        Phake::when($unitOfWork)->recomputeSingleDocumentChangeSet(Phake::anyParameters())->thenReturn('test');
        Phake::when($documentManager)->getClassMetadata(Phake::anyParameters())->thenReturn(new ClassMetadata('OpenOrchestra\ModelBundle\Document\Node'));
        Phake::when($documentManager)->getUnitOfWork()->thenReturn($unitOfWork);
        Phake::when($this->lifecycleEventArgs)->getDocument()->thenReturn($node);
        Phake::when($this->lifecycleEventArgs)->getDocumentManager()->thenReturn($documentManager);
        Phake::when($this->nodeRepository)->findChildsByPathAndSiteIdAndLanguage(Phake::anyParameters())->thenReturn($childs);

        $this->listener->$method($this->lifecycleEventArgs);

        Phake::verify($node, Phake::never())->setNodeId(Phake::anyParameters());
        Phake::verify($node)->setPath($expectedPath[0]);
        Phake::verify($documentManager, Phake::never())->getRepository(Phake::anyParameters());
        $count = 1;
        foreach ($childs as $child) {
            Phake::verify($child)->setPath($expectedPath[$count]);
            $count ++;
        }
    }

    /**
     *
     * @return array
     */
    public function provideNodeForRecord()
    {
        $node0 = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($node0)->getNodeId()->thenReturn('fakeId');
        Phake::when($node0)->getPath()->thenReturn('fakeParentPath/fakePastId');

        $parentNode0 = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($parentNode0)->getPath()->thenReturn('fakePath');
        Phake::when($parentNode0)->getPath()->thenReturn('fakeParentPath');

        $child0_0 = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($child0_0)->getPath()->thenReturn('fakeParentPath/fakePastId/fakeChild0Id');

        $childs0 = new ArrayCollection();
        $childs0->add($child0_0);

        $node1 = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($node1)->getNodeId()->thenReturn('fakeId');
        Phake::when($node1)->getPath()->thenReturn('fakeParentPath/fakePastId');

        $parentNode1 = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($parentNode1)->getPath()->thenReturn('fakePath');
        Phake::when($parentNode1)->getPath()->thenReturn('fakeParentPath');

        $child1_0 = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($child1_0)->getPath()->thenReturn('fakeParentPath/fakePastId/fakeChild0Id');

        $childs1 = new ArrayCollection();
        $childs1->add($child1_0);


        return array(
            array('prePersist', $node0, $parentNode0, $childs0, array('fakeParentPath/fakeId', 'fakeParentPath/fakeId/fakeChild0Id')),
            array('preUpdate', $node1, $parentNode1, $childs1, array('fakeParentPath/fakeId', 'fakeParentPath/fakeId/fakeChild0Id'))
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
}

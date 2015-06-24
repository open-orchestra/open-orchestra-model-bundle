<?php

namespace OpenOrchestra\ModelBundle\Tests\EventListener;

use Phake;
use OpenOrchestra\ModelBundle\EventListener\GenerateIdListener;
use OpenOrchestra\ModelInterface\Mapping\Annotations\Document;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Class GenerateIdListenerTest
 */
class GenerateIdListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $event;
    protected $listener;
    protected $container;
    protected $annotations;
    protected $documentManager;
    protected $suppressSpecialCharacterHelper;
    protected $annotationReader;
    protected $documentRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->suppressSpecialCharacterHelper = Phake::mock('OpenOrchestra\ModelInterface\Helper\SuppressSpecialCharacterHelperInterface');

        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        $this->annotationReader = Phake::mock('Doctrine\Common\Annotations\AnnotationReader');
        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $this->event = Phake::mock('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs');
        Phake::when($this->event)->getDocumentManager()->thenReturn($this->documentManager);

        $this->listener = new GenerateIdListener($this->annotationReader, $this->suppressSpecialCharacterHelper);
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
     * @param DocumentRepository $repository
     * @param Document           $generateAnnotations
     * @param NodeInterface      $node
     * @param string             $expectedId
     *
     * @dataProvider provideAnnotations
     */
    public function testPrePersist(DocumentRepository $repository, Document $generateAnnotations, NodeInterface $node, $expectedId)
    {
        Phake::when($this->annotationReader)->getClassAnnotation(Phake::anyParameters())->thenReturn($generateAnnotations);
        Phake::when($this->event)->getDocument()->thenReturn($node);
        Phake::when($this->container)->get(Phake::anyParameters())->thenReturn($repository);
        Phake::when($this->suppressSpecialCharacterHelper)->transform(Phake::anyParameters())->thenReturn($expectedId);

        $this->listener->prePersist($this->event);

        Phake::verify($generateAnnotations)->getSource($node);
        Phake::verify($generateAnnotations)->getGenerated($node);
        Phake::verify($generateAnnotations)->setGenerated($node);
        Phake::verify($node)->setNodeId($expectedId);

    }

    /**
     * @return array
     */
    public function provideAnnotations()
    {
        $document0 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($document0)->getNodeId()->thenReturn(null);

        $document2 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($document2)->getNodeId()->thenReturn(null);

        $document3 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($document3)->getNodeId()->thenReturn(null);

        $annotations0 = Phake::mock('OpenOrchestra\ModelInterface\Mapping\Annotations\Document');
        Phake::when($annotations0)->getGeneratedField(Phake::anyParameters())->thenReturn('nodeId');
        Phake::when($annotations0)->getSource(Phake::anyParameters())->thenReturn('getName');
        Phake::when($annotations0)->getGenerated(Phake::anyParameters())->thenReturn('getNodeId');
        Phake::when($annotations0)->setGenerated(Phake::anyParameters())->thenReturn('setNodeId');
        Phake::when($annotations0)->getTestMethod()->thenReturn('fakeMethod');

        $annotations2 = Phake::mock('OpenOrchestra\ModelInterface\Mapping\Annotations\Document');
        Phake::when($annotations2)->getGeneratedField(Phake::anyParameters())->thenReturn('nodeId');
        Phake::when($annotations2)->getSource(Phake::anyParameters())->thenReturn('getName');
        Phake::when($annotations2)->getGenerated(Phake::anyParameters())->thenReturn('getNodeId');
        Phake::when($annotations2)->setGenerated(Phake::anyParameters())->thenReturn('setNodeId');
        Phake::when($annotations2)->getTestMethod()->thenReturn('fakeMethod');

        $annotations3 = Phake::mock('OpenOrchestra\ModelInterface\Mapping\Annotations\Document');
        Phake::when($annotations3)->getGeneratedField(Phake::anyParameters())->thenReturn('nodeId');
        Phake::when($annotations3)->getSource(Phake::anyParameters())->thenReturn('getName');
        Phake::when($annotations3)->getGenerated(Phake::anyParameters())->thenReturn('getNodeId');
        Phake::when($annotations3)->setGenerated(Phake::anyParameters())->thenReturn('setNodeId');
        Phake::when($annotations3)->getTestMethod()->thenReturn(null);

        $repository0 = Phake::mock('Doctrine\ODM\MongoDB\DocumentRepository');
        Phake::when($repository0)->fakeMethod(Phake::anyParameters())->thenReturn(false);

        $repository2 = Phake::mock('Doctrine\ODM\MongoDB\DocumentRepository');
        Phake::when($repository2)->fakeMethod('fakename')->thenReturn(true);
        Phake::when($repository2)->fakeMethod('fakename_1')->thenReturn(false);

        $repository3 = Phake::mock('OpenOrchestra\ModelBundle\Repository\NodeRepository');
        Phake::when($repository3)->testUniquenessInContext('fakename')->thenReturn(false);

        return array(
            array($repository0, $annotations0, $document0, 'fakename'),
            array($repository2, $annotations2, $document2, 'fakename_1'),
            array($repository3, $annotations3, $document3, 'fakename'),
        );
    }

}

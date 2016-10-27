<?php

namespace OpenOrchestra\ModelBundle\Tests\EventListener;

use OpenOrchestra\ModelBundle\EventListener\UpdateBoLabelNodeFieldListener;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class UpdateBoLabelNodeFieldListenerTest
 */
class UpdateBoLabelNodeFieldListenerTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateBoLabelNodeFieldListener
     */
    protected $listener;

    protected $preUpdateEventArgs;
    protected $nodeRepository;
    protected $container;
    protected $dm;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\NodeRepository');
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        $this->preUpdateEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs');
        $this->dm = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        Phake::when($this->container)->get(Phake::anyParameters())->thenReturn($this->nodeRepository);
        Phake::when($this->preUpdateEventArgs)->getDocumentManager()->thenReturn($this->dm);

        $this->listener = new UpdateBoLabelNodeFieldListener();
        $this->listener->setContainer($this->container);
    }

    /**
     * Test if method is present
     */
    public function testCallable()
    {
        $this->assertTrue(is_callable(array($this->listener, 'preUpdate')));
    }

    /**
     * @param array $documents
     *
     * @dataProvider provideNodes
     */
    public function testPreUpdate(array $documents)
    {
        $boLabel = 'fakeBoLabel';
        $nodeId = 'fakeNodeId';
        $node = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($node)->getBoLabel()->thenReturn($boLabel);
        Phake::when($node)->getId()->thenReturn($nodeId);
        Phake::when($this->preUpdateEventArgs)->getDocument()->thenReturn($node);
        Phake::when($this->preUpdateEventArgs)->hasChangedField(Phake::anyParameters())->thenReturn(true);
        Phake::when($this->nodeRepository)->findByNodeAndSite(Phake::anyParameters())->thenReturn($documents);

        $this->listener->preUpdate($this->preUpdateEventArgs);

        foreach ($documents as $document) {
            Phake::verify($document)->setBoLabel($boLabel);
            Phake::verify($this->dm)->flush($document);
        }
    }

    /**
     * @return array
     */
    public function provideNodes()
    {
        $document0 = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($document0)->getBoLabel()->thenReturn("");
        Phake::when($document0)->getId()->thenReturn("fakeDocument0Id");
        $document1 = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($document1)->getBoLabel()->thenReturn("");
        Phake::when($document1)->getId()->thenReturn("fakeDocument1Id");
        $document2 = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($document2)->getBoLabel()->thenReturn("");
        Phake::when($document2)->getId()->thenReturn("fakeDocument2Id");

        return array(
            array(array($document0)),
            array(array($document1, $document2)),
            array(array()),
        );
    }
}

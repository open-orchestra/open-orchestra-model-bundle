<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventListener;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelBundle\Document\Status;
use OpenOrchestra\ModelBundle\EventListener\SetInitialStatusListener;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Class SetInitialStatusListenerTest
 */
class SetInitialStatusListenerTest extends AbstractBaseTestCase
{
    protected $listener;
    protected $lifecycleEventArgs;
    protected $container;
    protected $statusRepository;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\StatusRepository');
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        Phake::when($this->container)->get(Phake::anyParameters())->thenReturn($this->statusRepository);
        $this->lifecycleEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs');

        $this->listener = new SetInitialStatusListener();
        $this->listener->setContainer($this->container);
    }

    /**
     * Test if method is present
     */
    public function testCallable()
    {
        $this->assertTrue(is_callable(array(
            $this->listener,
            'prePersist'
        )));
    }

    /**
     * @param StatusableInterface $document
     * @param Status              $status
     * @param integer             $nbrCall
     *
     * @dataProvider provideNodeForPersist
     */
    public function testprePersist(StatusableInterface $document, Status $status, $nbrCall)
    {
        Phake::when($this->statusRepository)->findOneByInitial()->thenReturn($status);
        Phake::when($this->lifecycleEventArgs)->getDocument()->thenReturn($document);

        $this->listener->prePersist($this->lifecycleEventArgs);

        Phake::verify($document, Phake::times($nbrCall))->setStatus($status);
    }

    /**
     *
     * @return array
     */
    public function provideNodeForPersist()
    {
        $node = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        $status = Phake::mock('OpenOrchestra\ModelBundle\Document\Status');

        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content)->isStatusable()->thenReturn(false);

        return array(
            array($node, $status, 1),
            array($content, $status, 0),
        );
    }
}

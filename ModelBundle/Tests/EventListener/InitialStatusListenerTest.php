<?php

namespace OpenOrchestra\ModelBundle\Tests\EventListener;

use Phake;
use OpenOrchestra\ModelBundle\EventListener\InitialStatusListener;
use OpenOrchestra\ModelBundle\Document\Status;

/**
 * Class InitialStatusListenerTest
 */
class InitialStatusListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;
    protected $lifecycleEventArgs;
    protected $postFlushEventArgs;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->lifecycleEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs');
        $this->postFlushEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\PostFlushEventArgs');

        $this->listener = new InitialStatusListener();
    }

    /**
     * Test if method is present
     */
    public function testCallable()
    {
        $this->assertTrue(is_callable(array($this->listener, 'preUpdate')));
        $this->assertTrue(is_callable(array($this->listener, 'postFlush')));
    }

    /**
     * @param Status $status
     * @param array  $documents
     *
     * @dataProvider provideStatus
     */
    public function testPreUpdate(Status $status, $documents)
    {
        $documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $queryBuilder = Phake::mock('Doctrine\ODM\MongoDB\Query\Builder');
        $query = Phake::mock('Doctrine\ODM\MongoDB\Query\Query');
        $statusRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\StatusRepository');

        Phake::when($statusRepository)->findOtherByInitial(Phake::anyParameters())->thenReturn($documents);
        Phake::when($query)->execute()->thenReturn($documents);
        Phake::when($documentManager)->getRepository('OpenOrchestraModelBundle:Status')->thenReturn($statusRepository);
        Phake::when($statusRepository)->createQueryBuilder()->thenReturn($queryBuilder);
        Phake::when($this->lifecycleEventArgs)->getDocument()->thenReturn($status);
        Phake::when($this->lifecycleEventArgs)->getDocumentManager()->thenReturn($documentManager);

        $this->listener->preUpdate($this->lifecycleEventArgs);

        foreach ($documents as $document) {
            Phake::verify($document)->setInitial(false);
        }
    }

    /**
     * @return array
     */
    public function provideStatus()
    {
        $status = Phake::mock('OpenOrchestra\ModelBundle\Document\Status');
        Phake::when($status)->isPublished()->thenReturn(true);
        Phake::when($status)->isInitial()->thenReturn(true);

        $document0 = Phake::mock('OpenOrchestra\ModelBundle\Document\Status');
        Phake::when($document0)->isInitial()->thenReturn(true);

        return array(
            array($status, array($document0))
        );
    }
}

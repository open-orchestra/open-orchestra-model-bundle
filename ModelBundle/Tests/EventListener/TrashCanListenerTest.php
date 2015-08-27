<?php

namespace OpenOrchestra\ModelBundle\Tests\EventListener;

use OpenOrchestra\ModelBundle\EventListener\TrashCanListener;
use OpenOrchestra\ModelBundle\Document\TrashItem;
use Phake;

/**
 * Class TrashCanListerTest
 */
class TrashCanListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TrashCanListener
     */
    protected $listener;
    protected $documentManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $trashCanClass = 'OpenOrchestra\ModelBundle\Document\TrashItem';
        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $this->listener = new TrashCanListener($trashCanClass);
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
     * @param $document
     * @param $oldValueDeleted
     * @param $newValueDelete
     * @param $hasChangeField
     *
     * @dataProvider provideDocument
     */
    public function testPreUpdate($document, $oldValueDeleted, $newValueDelete, $hasChangeField)
    {
        $event = Phake::mock('Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs');
        Phake::when($event)->getDocumentManager()->thenReturn($this->documentManager);
        Phake::when($event)->getNewValue(Phake::anyParameters())->thenReturn($newValueDelete);
        Phake::when($event)->getOldValue(Phake::anyParameters())->thenReturn($oldValueDeleted);
        Phake::when($event)->hasChangedField(Phake::anyParameters())->thenReturn($hasChangeField);
        Phake::when($event)->getDocument()->thenReturn($document);
        $this->listener->preUpdate($event);
        if ($hasChangeField && $oldValueDeleted !== $newValueDelete) {
            $this->assertCount(1, $this->listener->entities);
            /** @var TrashItem $entity */
            $entity = $this->listener->entities[0];
            $this->assertSame($entity->getEntity(), $document);
            $this->assertSame($entity->getName(), $document->getTrashCanName());
        } else {
            $this->assertCount(0, $this->listener->entities);
        }
    }

    /**
     * @return array
     */
    public function provideDocument()
    {
        $document = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($document)->getTrashCanName()->thenReturn('phakeTrashCanLabel');

        return array(
            array($document, false, true, true),
            array($document, false, true, true),
            array($document, false, true, false),
            array($document, true, true, false),
            array($document, true, true, true),
        );
    }

    /**
     * @param array $trashItemEntities
     *
     * @dataProvider provideTrashItemEntities
     */
    public function testPostFlush($trashItemEntities)
    {
        $event = Phake::mock('Doctrine\ODM\MongoDB\Event\PostFlushEventArgs');
        Phake::when($event)->getDocumentManager()->thenReturn($this->documentManager);
        $this->listener->entities = $trashItemEntities;

        $this->listener->postFlush($event);

        foreach ($trashItemEntities as $trashItemEntity) {
            Phake::verify($this->documentManager, Phake::atLeast(1))->persist($trashItemEntity);
            Phake::verify($this->documentManager)->flush($trashItemEntity);
        }

        $this->assertEmpty($this->listener->entities);
    }

    /**
     * @return array
     */
    public function provideTrashItemEntities()
    {
        $trashItem1 = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');
        $trashItem2 = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');
        $trashItem3 = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');

        return array(
            array($trashItem1),
            array($trashItem1, $trashItem2),
            array($trashItem1, $trashItem2, $trashItem3),
        );
    }
}

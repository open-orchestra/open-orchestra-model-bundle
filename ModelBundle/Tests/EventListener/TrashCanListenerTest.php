<?php

namespace OpenOrchestra\ModelBundle\Tests\EventListener;

use OpenOrchestra\ModelBundle\EventListener\TrashCanListener;
use OpenOrchestra\ModelBundle\Document\TrashCan;
use Phake;

/**
 * Class TrashCanLister
 */
class TrashCanLister extends \PHPUnit_Framework_TestCase
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
            /**
             * @var TrashCan $entity
             */
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
     * @param array $TrashItemEntities
     *
     * @dataProvider provideTrashCanEntities
     */
    public function testPostFlush($TrashItemEntities)
    {
        $event = Phake::mock('Doctrine\ODM\MongoDB\Event\PostFlushEventArgs');
        Phake::when($event)->getDocumentManager()->thenReturn($this->documentManager);
        $this->listener->entities = $TrashItemEntities;

        $this->listener->postFlush($event);

        foreach ($TrashItemEntities as $trashItemEntity) {
            Phake::verify($this->documentManager, Phake::atLeast(1))->persist($trashItemEntity);
            Phake::verify($this->documentManager)->flush($trashItemEntity);
        }

        $this->assertEmpty($this->listener->entities);
    }

    /**
     * @return array
     */
    public function provideTrashCanEntities()
    {
        $trashCan1 = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');
        $trashCan2 = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');
        $trashCan3 = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');

        return array(
            array($trashCan1),
            array($trashCan1, $trashCan2),
            array($trashCan1, $trashCan2, $trashCan3),
        );
    }
}

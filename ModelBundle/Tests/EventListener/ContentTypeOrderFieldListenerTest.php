<?php

namespace OpenOrchestra\ModelBundle\Tests\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelBundle\EventListener\ContentTypeOrderFieldListener;
use Phake;

/**
 * Class ContentTypeOrderFieldListenerTest
 */
class ContentTypeOrderFieldListenerTest extends AbstractBaseTestCase
{
    /**
     * @var ContentTypeOrderFieldListener
     */
    protected $listener;
    protected $lifecycleEventArgs;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->lifecycleEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs');
        $this->listener = new ContentTypeOrderFieldListener();
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

    public function testPrePersistWithoutContentType()
    {
        $document = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->lifecycleEventArgs)->getDocument()->thenReturn($document);

        $this->listener->preUpdate($this->lifecycleEventArgs);

        Phake::verify($document, Phake::never())->setFields(Phake::anyParameters());
    }

    /**
     * @param $fields
     * @param $expectedFields
     *
     * @dataProvider provideFields
     */
    public function testPrePersistWithContentType($fields, $expectedFields)
    {
        $document = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($this->lifecycleEventArgs)->getDocument()->thenReturn($document);
        Phake::when($document)->getFields()->thenReturn($fields);

        $this->listener->preUpdate($this->lifecycleEventArgs);

        Phake::verify($document)->setFields($expectedFields);
    }

    /**
     * @return array
     */
    public function provideFields()
    {
        $field1 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field1)->getPosition()->thenReturn(0);

        $field2 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field2)->getPosition()->thenReturn(0);

        $field3 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field3)->getPosition()->thenReturn(2);

        $field4 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field4)->getPosition()->thenReturn(5);

        return array(
            array(new ArrayCollection(array($field1, $field2)), new ArrayCollection(array($field1, $field2))),
            array(new ArrayCollection(array($field2, $field1)), new ArrayCollection(array($field2, $field1))),
            array(new ArrayCollection(array($field2, $field3,  $field1)), new ArrayCollection(array($field1, $field2, $field3))),
            array(new ArrayCollection(array($field4, $field2, $field3,  $field1)), new ArrayCollection(array($field2, $field1, $field3, $field4))),
        );
    }
}

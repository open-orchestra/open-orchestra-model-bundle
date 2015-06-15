<?php

namespace OpenOrchestra\ModelBundle\Tests\EventListener;

use OpenOrchestra\ModelBundle\Document\Content;
use OpenOrchestra\ModelBundle\EventListener\UpdateNonTranslatableContentFieldsListener;
use Phake;

/**
 * Test UpdateNonTranslatableContentFieldsListenerTest
 */
class UpdateNonTranslatableContentFieldsListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateNonTranslatableContentFieldsListener
     */
    protected $listener;

    protected $contentTypeRepository;
    protected $contentRepository;
    protected $fieldId = 'field';
    protected $documentManager;
    protected $immutableData;
    protected $contentType;
    protected $container;
    protected $contents;
    protected $content;
    protected $event;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->immutableData = array('linkedToSite');

        $this->documentManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->event = Phake::mock('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs');
        Phake::when($this->event)->getDocumentManager()->thenReturn($this->documentManager);

        $this->content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $this->contents = array($this->content, $this->content);
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        Phake::when($this->contentRepository)->findByContentId(Phake::anyParameters())->thenReturn($this->contents);

        $field = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field)->getFieldId()->thenReturn($this->fieldId);
        Phake::when($field)->isTranslatable()->thenReturn(false);
        $this->contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($this->contentType)->getFields()->thenReturn(array($field));

        $this->contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        Phake::when($this->contentTypeRepository)->findOneByContentTypeIdInLastVersion(Phake::anyParameters())->thenReturn($this->contentType);

        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        Phake::when($this->container)->get('open_orchestra_model.repository.content')->thenReturn($this->contentRepository);
        Phake::when($this->container)->get('open_orchestra_model.repository.content_type')->thenReturn($this->contentTypeRepository);

        $this->listener = new UpdateNonTranslatableContentFieldsListener('OpenOrchestra\ModelBundle\Document\ContentAttribute', $this->immutableData);
        $this->listener->setContainer($this->container);
    }

    /**
     * Test if method exists
     */
    public function testMethodExist()
    {
        $this->assertTrue(method_exists($this->listener, 'preUpdate'));
    }

    /**
     * Test implementation
     */
    public function testIsContainerAware()
    {
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerAwareInterface', $this->listener);
    }

    /**
     * @param boolean $isLinkedToSite
     * @param string  $value
     *
     * @dataProvider provideLinkedToSiteAndValue
     */
    public function testPreUpdateWithExistingAttributes($isLinkedToSite, $value)
    {
        $attribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        Phake::when($attribute)->getValue()->thenReturn($value);
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content)->isLinkedToSite()->thenReturn($isLinkedToSite);
        Phake::when($content)->getAttributeByName($this->fieldId)->thenReturn($attribute);
        Phake::when($this->content)->getAttributeByName($this->fieldId)->thenReturn($attribute);
        Phake::when($this->event)->getDocument()->thenReturn($content);

        $this->listener->preUpdate($this->event);

        Phake::verify($this->content, Phake::times(2))->setLinkedToSite($isLinkedToSite);
        Phake::verify($attribute, Phake::times(2))->setValue($value);
        Phake::verify($this->documentManager, Phake::times(2))->flush($this->content);
    }

    /**
     * @return array
     */
    public function provideLinkedToSiteAndValue()
    {
        return array(
            array(true, 'foo'),
            array(false, 'foo'),
            array(true, 'bar'),
            array(false, 'bar'),
        );
    }

    /**
     * @param boolean $isLinkedToSite
     * @param string  $value
     *
     * @dataProvider provideLinkedToSiteAndValue
     */
    public function testPreUpdateWithNoExistingAttributes($isLinkedToSite, $value)
    {
        $attribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        Phake::when($attribute)->getValue()->thenReturn($value);
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content)->isLinkedToSite()->thenReturn($isLinkedToSite);
        Phake::when($content)->getAttributeByName($this->fieldId)->thenReturn($attribute);
        Phake::when($this->event)->getDocument()->thenReturn($content);

        $contentDocument = new Content();
        Phake::when($this->contentRepository)->findByContentId(Phake::anyParameters())->thenReturn(array($contentDocument));

        $this->listener->preUpdate($this->event);

        $this->assertSame($isLinkedToSite, $contentDocument->isLinkedToSite());
        $field = $contentDocument->getAttributeByName($this->fieldId);
        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface', $field);
        $this->assertSame($value, $field->getValue());
        $this->assertSame($this->fieldId, $field->getName());
        Phake::verify($this->documentManager)->flush($contentDocument);
    }
}

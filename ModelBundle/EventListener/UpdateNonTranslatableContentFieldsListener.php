<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class UpdateNonTranslatableContentFieldsListener
 */
class UpdateNonTranslatableContentFieldsListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $contentManaged = array();
    protected $contentAttributeClass;
    protected $immutableData;

    /**
     * @param string $contentAttributeClass
     * @param array  $immutableData
     */
    public function __construct($contentAttributeClass, array $immutableData = array())
    {
        $this->contentAttributeClass = $contentAttributeClass;
        $this->immutableData = $immutableData;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        if (!($object = $event->getDocument()) instanceof ContentInterface || in_array($object->getContentId(), $this->contentManaged)) {
            return;
        }

        $this->contentManaged[] = $object->getContentId();

        $contents = $this->getContentRepository()->findByContentId($object->getContentId());
        $contentType = $this->getContentTypeRepository()->findOneByContentTypeIdInLastVersion($object->getContentType());

        /** @var ContentInterface $content */
        foreach ($contents as $content) {
            if ($content != $object) {
                foreach ($this->immutableData as $immutableData) {
                    $getter = $this->generateGetter($immutableData, $object);
                    $setter = 'set' . ucfirst($immutableData);
                    $content->$setter($object->$getter());
                }
                /** @var FieldTypeInterface $field */
                foreach ($contentType->getFields() as $field) {
                    if (!$field->isTranslatable()) {
                        $contentAttribute = $this->getContentAttribute($content, $field->getFieldId());
                        $contentAttribute->setValue($object->getAttributeByName($field->getFieldId())->getValue());
                    }
                }

            }
            $event->getDocumentManager()->flush($content);
        }
    }

    /**
     * @return ContentRepositoryInterface
     */
    protected function getContentRepository()
    {
        return $this->container->get('open_orchestra_model.repository.content');
    }

    /**
     * @param string           $immutableData
     * @param ContentInterface $object
     * @return string
     */
    protected function generateGetter($immutableData, ContentInterface $object)
    {
        foreach (array('get', 'is', 'has') as $prefix) {
            $getter = $prefix . ucfirst($immutableData);
            if (method_exists($object, $getter)) {
                return $getter;
            }
        }

        return $immutableData;
    }

    /**
     * @return ContentTypeRepositoryInterface
     */
    protected function getContentTypeRepository()
    {
        return $this->container->get('open_orchestra_model.repository.content_type');
    }

    /**
     * @param ContentInterface $content
     * @param string           $fieldId
     *
     * @return ContentAttributeInterface
     */
    protected function getContentAttribute(ContentInterface $content, $fieldId)
    {
        $contentAttribute = $content->getAttributeByName($fieldId);

        if (!$contentAttribute instanceof ContentAttributeInterface) {
            $contentAttributeClass = $this->contentAttributeClass;
            /** @var ContentAttributeInterface $contentAttribute */
            $contentAttribute = new $contentAttributeClass();
            $contentAttribute->setName($fieldId);
            $content->addAttribute($contentAttribute);
        }

        return $contentAttribute;
    }
}

<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\Container;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;

/**
 * Class GenerateIdListener
 */
class GenerateIdListener
{
    protected $container;
    protected $annotationReader;

    /**
     * @param Container        $container
     * @param AnnotationReader $annotationReader
     */
    public function __construct(Container $container, AnnotationReader $annotationReader)
    {
        $this->container = $container;
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $document = $event->getDocument();
        $className = get_class($document);
        $generateAnnotations = $this->annotationReader->getClassAnnotation(new \ReflectionClass($className), 'OpenOrchestra\ModelInterface\Mapping\Annotations\Document');
        if (!is_null($generateAnnotations)) {
            $repository = $this->container->get($generateAnnotations->getServiceName());

            $getSource = $generateAnnotations->getSource($document);
            $getGenerated = $generateAnnotations->getGenerated($document);
            $setGenerated = $generateAnnotations->setGenerated($document);
            $testMethod = $generateAnnotations->getTestMethod();
            if ($testMethod === null && $repository instanceof FieldAutoGenerableRepositoryInterface) {
                $testMethod = 'testUnicityInContext';
            }
            if (is_null($document->$getGenerated())) {
                $accents = '/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig|tilde);/';
                $sourceField = $document->$getSource();
                $sourceField = htmlentities($sourceField, ENT_NOQUOTES, 'UTF-8');
                $sourceField = preg_replace($accents, '$1', $sourceField);
                $sourceField = preg_replace('/[[:^alnum:]]+/', '-', $sourceField);
                $sourceField = trim($sourceField, '-');
                $sourceField = strtolower($sourceField);
                $generatedField = $sourceField;
                $count = 1;
                while ($repository->$testMethod($generatedField)) {
                    $generatedField = $sourceField . '-' . $count;
                    $count++;
                }

                $document->$setGenerated($generatedField);
            }
        }
    }
}

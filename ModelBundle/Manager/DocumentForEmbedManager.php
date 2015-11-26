<?php

namespace OpenOrchestra\ModelBundle\Manager;

use OpenOrchestra\ModelInterface\Manager\DocumentForEmbedManagerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Persisters\PersistenceBuilder;

class DocumentForEmbedManager implements DocumentForEmbedManagerInterface
{
    protected $documentManager;
    protected $documentClass;

    /**
     * @param DocumentManager $documentManager
     * @param string          $documentClass
     */
    public function __construct(DocumentManager $documentManager, $documentClass)
    {
        $this->documentManager = $documentManager;
        $this->documentClass = $documentClass;
    }

    /**
     * Take a embed document array representation to return id
     *
     * @param array $data
     *
     * @return string
     */
    public function fromDbToEntity($data)
    {
        $hydratorFactory = $this->documentManager->getHydratorFactory();
        $documentClass = $this->documentClass;

        $document = new $documentClass();
        $hydratorFactory->hydrate($document, $data);

        return $document;
    }

    /**
     * Take a id to turn it into a embed document array representation
     *
     * @param array $data
     *
     * @return array
     */
    public function fromEntityToDb($id)
    {
        $document = $this->documentManager->find($this->documentClass, $id);
        $unitOfWork = $this->documentManager->getUnitOfWork();
        $persistenceBuilder = new PersistenceBuilder($this->documentManager, $unitOfWork);
        $mapping = array (
            'targetDocument' => $this->documentClass,
        );

        return $persistenceBuilder->prepareEmbeddedDocumentValue($mapping, $document, true);
    }
}

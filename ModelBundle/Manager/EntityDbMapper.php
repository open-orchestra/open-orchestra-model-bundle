<?php

namespace OpenOrchestra\ModelBundle\Manager;

use OpenOrchestra\ModelInterface\Manager\EntityDbMapperInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Persisters\PersistenceBuilder;

/**
 * Class EntityDbMapper
 */
class EntityDbMapper implements EntityDbMapperInterface
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
     * Take a embed document array representation to return entity
     *
     * @param array $data
     *
     * @return mixed
     */
    public function fromDbToEntity(array $data)
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
     * @param mixed $document
     *
     * @return array
     */
    public function fromEntityToDb($document)
    {
        $unitOfWork = $this->documentManager->getUnitOfWork();
        $persistenceBuilder = new PersistenceBuilder($this->documentManager, $unitOfWork);
        $mapping = array (
            'targetDocument' => $this->documentClass,
        );

        return $persistenceBuilder->prepareEmbeddedDocumentValue($mapping, $document, true);
    }
}

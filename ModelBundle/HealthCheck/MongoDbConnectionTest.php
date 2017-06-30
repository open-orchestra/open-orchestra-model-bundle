<?php

namespace OpenOrchestra\ModelBundle\HealthCheck;

use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\BaseBundle\HealthCheck\AbstractHealthCheckTest;
use OpenOrchestra\BaseBundle\HealthCheck\HealthCheckTestResultInterface;

/**
 * Class MongoDbConnectionTest
 */
class MongoDbConnectionTest extends AbstractHealthCheckTest
{
    protected $documentManager;

    /**
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $label = "Mongo DB connection";
        if (false === $this->documentManager->getConnection()->isConnected()) {
            return $this->createTestResult(true, $label, HealthCheckTestResultInterface::ERROR);
        }

        return $this->createValidTestResult($label);
    }
}

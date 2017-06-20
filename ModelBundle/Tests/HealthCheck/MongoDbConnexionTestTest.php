<?php

namespace OpenOrchestra\FrontBundle\Tests\HealthCheck;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\BaseBundle\HealthCheck\HealthCheckTestResult;
use OpenOrchestra\BaseBundle\HealthCheck\HealthCheckTestResultInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelBundle\HealthCheck\MongoDbConnectionTest;
use Phake;

/**
 * Class MongoDbConnectionTestTest
 */
class MongoDbConnectionTestTest extends AbstractBaseTestCase
{
    /** @var MongoDbConnectionTest */
    protected $test;
    protected $dm;

    public function setUp()
    {
        $this->dm = Phake::mock(DocumentManager::class);

        $this->test = new MongoDbConnectionTest($this->dm);
        $this->test->setHealthCheckResultClass(HealthCheckTestResult::class);
    }

    /**
     * @param bool $isConnected
     * @param bool $error
     * @param int  $level
     *
     * @dataProvider provideRequestHeader
     */
    public function testRun($isConnected, $error, $level)
    {
        $connexion = Phake::mock(Connection::class);
        Phake::when($this->dm)->getConnection()->thenReturn($connexion);
        Phake::when($connexion)->isConnected()->thenReturn($isConnected);

        $result = $this->test->run();
        $this->assertInstanceOf(HealthCheckTestResult::class, $result);
        $this->assertEquals($error, $result->isError());
        $this->assertEquals($level, $result->getLevel());
    }

    /**
     * @return array
     */
    public function provideRequestHeader()
    {
        return array(
            array(false, true, HealthCheckTestResultInterface::ERROR),
            array(true, false, HealthCheckTestResultInterface::OK),
        );
    }
}

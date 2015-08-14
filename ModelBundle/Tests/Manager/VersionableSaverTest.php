<?php

namespace OpenOrchestra\ModelBundle\Tests\Manager;

use OpenOrchestra\ModelBundle\Manager\VersionableSaver;
use OpenOrchestra\ModelInterface\Manager\VersionableSaverInterface;
use Phake;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;

/**
 * Class VersionableSaverTest
 */
class VersionableSaverTest extends \PHPUnit_Framework_TestCase
{
    /** @var  VersionableSaverInterface */
    protected $versionableSaver;
    protected $container;
    protected $documentManager;
    protected $database;
    protected $connection;
    protected $hydratorFactory;

    /**
     * set up the test
     */
    public function setUp()
    {
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $this->database = Phake::mock('Doctrine\MongoDB\LoggableDatabase');
        $this->connection = Phake::mock('Doctrine\MongoDB\Connection');
        $this->hydratorFactory = Phake::mock('Doctrine\ODM\MongoDB\Hydrator\HydratorFactory');


        Phake::when($this->documentManager)->getDocumentDatabase(Phake::anyParameters())->thenReturn($this->database);
        Phake::when($this->documentManager)->getConnection()->thenReturn($this->connection);
        Phake::when($this->documentManager)->getHydratorFactory()->thenReturn($this->hydratorFactory);
        Phake::when($this->container)->get(Phake::anyParameters())->thenReturn($this->documentManager);

        $this->versionableSaver = new VersionableSaver();
        $this->versionableSaver->setContainer($this->container);
    }

    /**
     * test duplicateNode
     */
    public function testSaveDuplicated()
    {
        $version = 1;
        $versionable = Phake::mock('OpenOrchestra\ModelInterface\Model\VersionableInterface');
        Phake::when($versionable)->getVersion()->thenReturn($version);

        Phake::when($this->documentManager)->flush(Phake::anyParameters())
            ->thenThrow(new DuplicateKeyException())
            ->thenThrow(new DuplicateKeyException())
            ->thenThrow(new DuplicateKeyException())
            ->thenReturn(null);

        $newVersionable = $this->versionableSaver->saveDuplicated($versionable);

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\VersionableInterface', $newVersionable);
        Phake::verify($this->container)->get('doctrine.odm.mongodb.document_manager');
        Phake::verify($versionable)->setVersion(2);
        Phake::verify($versionable)->setVersion(3);
        Phake::verify($versionable)->setVersion(4);
        Phake::verify($versionable, Phake::never())->setVersion(5);
        Phake::verify($this->documentManager, Phake::times(4))->flush(Phake::anyParameters());
    }
}

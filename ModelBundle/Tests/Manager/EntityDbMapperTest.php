<?php

namespace OpenOrchestra\ModelBundle\Tests\Manager;

use OpenOrchestra\ModelInterface\Manager\EntityDbMapperInterface;
use OpenOrchestra\ModelBundle\Manager\EntityDbMapper;
use Phake;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;

/**
 * Class EntityDbMapperTest
 */
class EntityDbMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var  EntityDbMapperInterface */
    protected $entityDbMapper;
    protected $fakeClass = 'OpenOrchestra\ModelBundle\Tests\Manager\FakeDocument';
    protected $fakeProperty = 'fakeProperty';
    protected $fakePropertyValue = 'fakePropertyValue';

    /**
     * set up the test
     */
    public function setUp()
    {
        $documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $metaData = Phake::mock('Doctrine\ODM\MongoDB\Mapping\ClassMetadata');
        $unitOfWork = Phake::mock('Doctrine\ODM\MongoDB\UnitOfWork');
        $hydratorFactory = Phake::mock('\Doctrine\ODM\MongoDB\Hydrator\HydratorInterface');

        $metaData->reflFields = array(
            $this->fakeProperty => new \ReflectionProperty($this->fakeClass, $this->fakeProperty)
        );
        $metaData->fieldMappings = array(
            $this->fakeProperty => array(
                'fieldName' => $this->fakeProperty,
                'name' => $this->fakeProperty,
                'type' => 'string'
            )
        );

        Phake::when($documentManager)->getUnitOfWork()->thenReturn($unitOfWork);
        Phake::when($documentManager)->getUnitOfWork()->thenReturn($unitOfWork);
        Phake::when($documentManager)->getClassMetadata($this->fakeClass)->thenReturn($metaData);
        Phake::when($documentManager)->getHydratorFactory()->thenReturn($hydratorFactory);

        $this->entityDbMapper = new EntityDbMapper($documentManager, $this->fakeClass);
    }

    /**
     * test fromDbToEntity
     */
    public function testFromDbToEntity()
    {
        $data = array($this->fakeProperty => $this->fakePropertyValue);
        $fakeDocument = $this->entityDbMapper->fromDbToEntity($data);

        $this->assertInstanceOf($this->fakeClass, $fakeDocument);
    }

    /**
     * test fromEntityToDb
     */
    public function testFromEntityToDb()
    {
        $fakeClass = $this->fakeClass;
        $document = new $fakeClass();
        $document->setFakeProperty($this->fakePropertyValue);

        $fakeArray = $this->entityDbMapper->fromEntityToDb($document);

        $this->assertEquals(array($this->fakeProperty => $this->fakePropertyValue), $fakeArray);
    }
}

/**
 * Class FakeDocument
 */
class FakeDocument
{
    public $fakeProperty;

    public function getFakeProperty() {
        return $this->fakeProperty;
    }

    public function setFakeProperty($fakeProperty) {
        $this->fakeProperty = $fakeProperty;
    }
}

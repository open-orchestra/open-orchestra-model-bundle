<?php

namespace OpenOrchestra\ModelBundle\Tests\Manager;

use OpenOrchestra\ModelInterface\Manager\NodeManagerInterface;
use Phake;
use OpenOrchestra\ModelBundle\Manager\NodeManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;

/**
 * Class NodeManagerTest
 */
class NodeManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeManagerInterface
     */
    protected $manager;

    protected $nodeClass;
    protected $areaClass;
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

        $this->nodeClass = 'OpenOrchestra\ModelBundle\Document\Node';
        $this->areaClass = 'OpenOrchestra\ModelBundle\Document\Area';
        $this->manager = new NodeManager($this->nodeClass, $this->areaClass);
        $this->manager->setContainer($this->container);
    }

    /**
     * @param string $language
     * @param string $siteId
     *
     * @dataProvider provideLanguageAndSite
     */
    public function testCreateTransverseNode($language, $siteId)
    {
        $node = $this->manager->createTransverseNode($language, $siteId);

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\NodeInterface', $node);
        $this->assertSame($siteId, $node->getSiteId());
        $this->assertSame(NodeInterface::TRANSVERSE_NODE_ID, $node->getNodeId());
        $this->assertSame(NodeInterface::TRANSVERSE_NODE_ID, $node->getName());
        $this->assertSame(NodeInterface::TYPE_TRANSVERSE, $node->getNodeType());
        $this->assertSame(1, $node->getVersion());
        $this->assertSame($language, $node->getLanguage());
        $this->assertCount(1, $node->getAreas());
        $area = $node->getAreas()->first();
        $this->assertSame('main', $area->getLabel());
        $this->assertSame('main', $area->getAreaId());
    }

    /**
     * @return array
     */
    public function provideLanguageAndSite()
    {
        return array(
            array('fr', '1'),
            array('en', '1'),
            array('fr', '2'),
            array('en', '2'),
        );
    }

    /**
     * test duplicateNode
     */
    public function testSaveDuplicatedNode()
    {
        $version = 1;
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getVersion()->thenReturn($version);

        Phake::when($this->documentManager)->flush(Phake::anyParameters())
            ->thenThrow(new DuplicateKeyException())
            ->thenThrow(new DuplicateKeyException())
            ->thenThrow(new DuplicateKeyException())
            ->thenReturn(null);

        $newNode = $this->manager->saveDuplicatedNode($node);

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\NodeInterface', $newNode);
        Phake::verify($this->container)->get('doctrine.odm.mongodb.document_manager');
        Phake::verify($node)->setVersion(2);
        Phake::verify($node)->setVersion(3);
        Phake::verify($node)->setVersion(4);
        Phake::verify($node, Phake::never())->setVersion(5);
        Phake::verify($this->documentManager, Phake::times(4))->flush(Phake::anyParameters());
    }
}

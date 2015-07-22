<?php

namespace OpenOrchestra\ModelBundle\Tests\Manager;

use Phake;
use OpenOrchestra\ModelBundle\Manager\NodeManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class NodeManagerTest
 */
class NodeManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;

    protected $nodeClass;
    protected $areaClass;
    protected $container;

    /**
     * set up the test
     */
    public function setUp()
    {
        $container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        $documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $database = Phake::mock('Doctrine\MongoDB\LoggableDatabase');
        $connection = Phake::mock('Doctrine\MongoDB\Connection');
        $hydratorFactory = Phake::mock('Doctrine\ODM\MongoDB\Hydrator\HydratorFactory');

        Phake::when($database)->execute(Phake::anyParameters())->thenReturn(array('retval' => 'fakeRateVal'));
        Phake::when($documentManager)->getDocumentDatabase(Phake::anyParameters())->thenReturn($database);
        Phake::when($documentManager)->getConnection()->thenReturn($connection);
        Phake::when($documentManager)->getHydratorFactory()->thenReturn($hydratorFactory);
        Phake::when($container)->get(Phake::anyParameters())->thenReturn($documentManager);

        $this->nodeClass = 'OpenOrchestra\ModelBundle\Document\Node';
        $this->areaClass = 'OpenOrchestra\ModelBundle\Document\Area';
        $this->manager = new NodeManager($this->nodeClass, $this->areaClass);
        $this->manager->setContainer($container);
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
    public function testduplicateNode()
    {
        $nodeId = 'fakeNodeId';
        $siteId = 'fakeSiteId';
        $language = 'fakeLanguage';
        $status = null;

        $node = $this->manager->duplicateNode($nodeId, $siteId, $language, $status);

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\NodeInterface', $node);
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Tests\Functional\Repository;

use Phake;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelBundle\Repository\NodeRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class NodeRepositoryTest
 */
class NodeRepositoryTest extends KernelTestCase
{
    /**
     * @var NodeRepository
     */
    protected $repository;

    protected $currentSiteManager;

    /**
     * Set up test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->currentSiteManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        Phake::when($this->currentSiteManager)->getCurrentSiteId()->thenReturn('1');
        Phake::when($this->currentSiteManager)->getCurrentSiteDefaultLanguage()->thenReturn('fr');

        static::bootKernel();
        $this->repository = static::$kernel->getContainer()->get('open_orchestra_model.repository.node');
    }

    /**
     * @param string $language
     * @param int    $version
     * @param string $siteId
     *
     * @dataProvider provideLanguageLastVersionAndSiteId
     */
    public function testFindOneByNodeIdAndLanguageWithPublishedAndLastVersionAndSiteId($language, $version, $siteId)
    {
        $node = $this->repository->findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::ROOT_NODE_ID, $language, $siteId);

        $this->assertSameNode($language, $version, $siteId, $node);
    }

    /**
     * @return array
     */
    public function provideLanguageLastVersionAndSiteId()
    {
        return array(
            array('en', 1, '1'),
            array('fr', 2, '1'),
            array('fr', 1, '2'),
        );
    }

    /**
     * @param $language
     * @param $version
     * @param $siteId
     *
     * @dataProvider provideLanguageLastVersionAndSiteId
     */
    public function testFindOneByNodeIdAndLanguageAndVersionAndSiteIdWithPublishedDataSet($language, $version, $siteId)
    {
        $node = $this->repository->findOneByNodeIdAndLanguageAndSiteIdAndVersion(NodeInterface::ROOT_NODE_ID, $language, $siteId, $version);

        $this->assertSameNode($language, $version, $siteId, $node);
    }

    /**
     * @param string $language
     * @param int    $version
     * @param string $siteId
     * @param int    $versionExpected
     *
     * @dataProvider provideLanguageLastVersionAndSiteIdNotPublished
     */
    public function testFindOneByNodeIdAndLanguageAndVersionAndSiteIdWithNotPublishedDataSet($language, $version = null, $siteId, $versionExpected)
    {
        $node = $this->repository->findOneByNodeIdAndLanguageAndSiteIdAndVersion(NodeInterface::ROOT_NODE_ID, $language, $siteId, $version);

        $this->assertSameNode($language, $versionExpected, $siteId, $node);
        $this->assertSame('draft', $node->getStatus()->getName());
    }

    /**
     * @return array
     */
    public function provideLanguageLastVersionAndSiteIdNotPublished()
    {
        return array(
            array('fr', 3, '1', 3),
            array('fr', null, '1', 3),
        );
    }

    /**
     * @param string $language
     * @param int    $version
     * @param string $siteId
     * @param int    $versionExpected
     *
     * @dataProvider provideLanguageLastVersionAndSiteIdNotPublished
     */
    public function testFindOneByNodeIdAndLanguageAndSiteIdAndLastVersion($language, $version = null, $siteId, $versionExpected)
    {
        $node = $this->repository->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::ROOT_NODE_ID, $language, $siteId);

        $this->assertSameNode($language, $versionExpected, $siteId, $node);
    }

    /**
     * @param array  $versions
     * @param string $language
     * @param string $siteId
     *
     * @dataProvider provideLanguageAndVersionListAndSiteId
     */
    public function testFindByNodeIdAndLanguageAndSiteId(array $versions, $language, $siteId)
    {
        $nodes = $this->repository->findByNodeIdAndLanguageAndSiteId(NodeInterface::ROOT_NODE_ID, $language, $siteId);

        $this->assertCount(count($versions), $nodes);
        foreach ($nodes as $node) {
            $this->assertSameNode($language, array_shift($versions), $siteId, $node);
        }
    }

    /**
     * @return array
     */
    public function provideLanguageAndVersionListAndSiteId()
    {
        return array(
            array(array(1), 'en', '1'),
            array(array(1, 2, 3), 'fr', '1'),
            array(array(1), 'fr', '2'),
        );
    }

    /**
     * @param string $nodeId
     * @param string $siteId
     * @param int    $count
     *
     * @dataProvider provideNodeIdSiteIdAndCount
     */
    public function testFindByNodeIdAndSiteId($nodeId, $siteId, $count)
    {
        $nodes = $this->repository->findByNodeIdAndSiteId($nodeId, $siteId);
        $this->assertCount($count, $nodes);
    }

    /**
     * @return array
     */
    public function provideNodeIdSiteIdAndCount()
    {
        return array(
            array(NodeInterface::ROOT_NODE_ID, '1', 4),
            array(NodeInterface::TRANSVERSE_NODE_ID, '2', 2),
            array('fixture_page_what_is_orchestra', '2', 0),
        );
    }



    /**
     * @param string $parentId
     * @param string $siteId
     * @param int    $count
     *
     * @dataProvider provideParentIdSiteIdAndCount
     */
    public function testFindByParentIdAndSiteId($parentId, $siteId, $count)
    {
        $nodes = $this->repository->findByParentIdAndSiteId($parentId, $siteId);

        $this->assertGreaterThanOrEqual($count, count($nodes));
    }

    /**
     * @return array
     */
    public function provideParentIdSiteIdAndCount()
    {
        return array(
            array(NodeInterface::ROOT_NODE_ID, '1', 7),
            array('fixture_about_us', '1', 2),
            array(NodeInterface::TRANSVERSE_NODE_ID, '2', 0),
            array('fixture_page_what_is_orchestra', '2', 0),
        );
    }

    /**
     * @param string $siteId
     * @param int    $nodeNumber
     * @param int    $version
     *
     * @dataProvider provideSiteIdAndNumberOfNode
     */
    public function testFindLastVersionBySiteId($siteId, $nodeNumber, $version)
    {
        $nodes = $this->repository->findLastVersionBySiteId($siteId);

        $this->assertCount($nodeNumber, $nodes);
        $this->assertSameNode('fr', $version, $siteId, $nodes[NodeInterface::ROOT_NODE_ID]);
    }

    /**
     * @return array
     */
    public function provideSiteIdAndNumberOfNode()
    {
        return array(
            array('2', 5, 1),
        );
    }

    /**
     * @param string        $language
     * @param int           $version
     * @param string        $siteId
     * @param NodeInterface $node
     * @param string        $nodeId
     */
    protected function assertSameNode($language, $version, $siteId, $node, $nodeId = NodeInterface::ROOT_NODE_ID)
    {
        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\NodeInterface', $node);
        $this->assertSame($nodeId, $node->getNodeId());
        $this->assertSame($language, $node->getLanguage());
        $this->assertSame($version, $node->getVersion());
        $this->assertSame($siteId, $node->getSiteId());
        $this->assertSame(false, $node->getDeleted());
    }

    /**
     * @param string      $siteId
     * @param int         $nodeNumber
     * @param int         $version
     * @param string      $language
     * @param string|null $nodeId
     *
     * @dataProvider provideForGetFooter()
     */
    public function testGetFooterTree($siteId, $nodeNumber, $version, $language = 'fr', $nodeId = null)
    {
        $nodes = $this->repository->getFooterTreeByLanguageAndSiteId($language, $siteId);

        $this->assertCount($nodeNumber, $nodes);
        if ($nodeId) {
            $this->assertSameNode($language, $version, $siteId, $nodes[$nodeId], $nodeId);
            $this->assertSame('published', $nodes[$nodeId]->getStatus()->getName());
        }
    }

    /**
     * @return array
     */
    public function provideForGetFooter()
    {
        return array(
            array('1', 6, 1, 'fr', 'fixture_about_us'),
            array('1', 0, 1, 'en'),
            array('2', 1, 1),
        );
    }

    /**
     * @param string      $siteId
     * @param int         $nodeNumber
     * @param int         $version
     * @param string      $language
     *
     * @dataProvider provideForGetMenu()
     */
    public function testGetMenuTree($siteId, $nodeNumber, $version, $language = 'fr')
    {
        $nodes = $this->repository->getMenuTreeByLanguageAndSiteId($language, $siteId);

        $this->assertCount($nodeNumber, $nodes);
        $this->assertSameNode($language, $version, $siteId, $nodes[NodeInterface::ROOT_NODE_ID]);
        $this->assertSame('published', $nodes[NodeInterface::ROOT_NODE_ID]->getStatus()->getName());
    }

    /**
     * @return array
     */
    public function provideForGetMenu()
    {
        return array(
            array('1', 8, 2, 'fr'),
            array('1', 1, 1, 'en'),
            array('2', 4, 1, 'fr'),
        );
    }

    /**
     * @param string      $nodeId
     * @param int         $nbLevel
     * @param int         $nodeNumber
     * @param int         $version
     * @param string      $siteId
     * @param string|null $local
     *
     * @dataProvider provideForGetSubMenu
     */
    public function testGetSubMenu($nodeId, $nbLevel, $nodeNumber, $version, $siteId, $local = null)
    {
        if (is_null($local)) {
            $local = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
        }
        $nodes = $this->repository->getSubMenuByNodeIdAndNbLevelAndLanguageAndSiteId($nodeId, $nbLevel, $local, $siteId);

        $this->assertCount($nodeNumber, $nodes);
        $this->assertSameNode($local, $version, $siteId, $nodes[0], $nodeId);

        $this->assertSame('published', $nodes[0]->getStatus()->getName());
    }

    /**
     * @return array
     */
    public function provideForGetSubMenu()
    {
        return array(
            array('fixture_about_us', 1, 3, 1, '1', 'fr'),
            array(NodeInterface::ROOT_NODE_ID, 1, 8, 2, '1', 'fr'),
            array(NodeInterface::ROOT_NODE_ID, 0, 6, 2, '1', 'fr'),
            array(NodeInterface::ROOT_NODE_ID, 1, 1, 1, '1', 'en'),
            array(NodeInterface::ROOT_NODE_ID, 0, 6, 2, '1'),
            array(NodeInterface::ROOT_NODE_ID, 1, 5, 1, '2', 'fr'),
            array(NodeInterface::ROOT_NODE_ID, 1, 5, 1, '2'),
        );
    }



    /**
     * @param string $siteId
     * @param int    $count
     *
     * @dataProvider provideSiteIdAndDeletedCount
     */
    public function testFindLastVersionByDeletedAndSiteId($siteId, $count)
    {
        $this->assertCount($count, $this->repository->findDeletedInLastVersionBySiteId($siteId));
    }

    /**
     * @return array
     */
    public function provideSiteIdAndDeletedCount()
    {
        return array(
            array('1', 3),
            array('2', 0),
        );
    }

    /**
     * @param string $language
     * @param string $siteId
     * @param int    $count
     *
     * @dataProvider provideLanguageSiteIdAndCount
     */
    public function testFindLastPublishedVersionByLanguageAndSiteId($language, $siteId, $count)
    {
        $nodes = $this->repository->findLastPublishedVersionByLanguageAndSiteId($language, $siteId);

        $this->assertCount($count, $nodes);
        foreach ($nodes as $node) {
            $this->assertSame($language, $node->getLanguage());
        }
    }

    /**
     * @return array
     */
    public function provideLanguageSiteIdAndCount()
    {
        return array(
            array('en', '2', 1),
            array('fr', '2', 5),
        );
    }
}

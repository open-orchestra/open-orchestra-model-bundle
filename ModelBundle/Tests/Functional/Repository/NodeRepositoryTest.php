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

    /**
     * Set up test
     */
    protected function setUp()
    {
        parent::setUp();

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
    public function testfindOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion($language, $version, $siteId)
    {
        $node = $this->repository->findPublishedInLastVersion(NodeInterface::ROOT_NODE_ID, $language, $siteId);

        $this->assertSameNode($language, $version, $siteId, $node);
    }

    /**
     * @return array
     */
    public function provideLanguageLastVersionAndSiteId()
    {
        return array(
            array('en', 1, '2'),
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
        $node = $this->repository->findVersion(NodeInterface::ROOT_NODE_ID, $language, $siteId, $version);

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
        $node = $this->repository->findVersion(NodeInterface::ROOT_NODE_ID, $language, $siteId, $version);

        $this->assertSameNode($language, $versionExpected, $siteId, $node);
        $this->assertSame('draft', $node->getStatus()->getName());
    }

    /**
     * @return array
     */
    public function provideLanguageLastVersionAndSiteIdNotPublished()
    {
        return array(
            array('fr', 2, '2', 2),
            array('fr', null, '2', 2),
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
    public function testFindInLastVersion($language, $version = null, $siteId, $versionExpected)
    {
        $node = $this->repository->findInLastVersion(NodeInterface::ROOT_NODE_ID, $language, $siteId);

        $this->assertSameNode($language, $versionExpected, $siteId, $node);
    }

    /**
     * @param int    $countVersions
     * @param string $language
     * @param string $siteId
     *
     * @dataProvider provideLanguageAndVersionListAndSiteId
     */
    public function testFindByNodeAndLanguageAndSite($countVersions, $language, $siteId)
    {
        $nodes = $this->repository->findByNodeAndLanguageAndSite(NodeInterface::ROOT_NODE_ID, $language, $siteId);

        $this->assertCount($countVersions, $nodes);
        foreach ($nodes as $node) {
            $this->assertSameNode($language, $node->getVersion(), $siteId, $node);
        }
    }

    /**
     * @return array
     */
    public function provideLanguageAndVersionListAndSiteId()
    {
        return array(
            array(1, 'en', '2'),
            array(2, 'fr', '2'),
        );
    }

    /**
     * @param string $nodeId
     * @param string $siteId
     * @param int    $count
     *
     * @dataProvider provideNodeSiteAndCount
     */
    public function testFindByNodeAndSite($nodeId, $siteId, $count)
    {
        $this->assertCount($count, $this->repository->findByNodeAndSite($nodeId, $siteId));
    }

    /**
     * @return array
     */
    public function provideNodeSiteAndCount()
    {
        return array(
            array(NodeInterface::ROOT_NODE_ID, '2', 3),
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
    public function testFindByParent($parentId, $siteId, $count)
    {
        $nodes = $this->repository->findByParent($parentId, $siteId);

        $this->assertGreaterThanOrEqual($count, count($nodes));
    }

    /**
     * @return array
     */
    public function provideParentIdSiteIdAndCount()
    {
        return array(
            array(NodeInterface::ROOT_NODE_ID, '2', 5),
            array('fixture_page_community', '2', 0),
            array(NodeInterface::TRANSVERSE_NODE_ID, '2', 0),
            array('fixture_page_what_is_orchestra', '2', 0),
        );
    }


    /**
     * @param string $path
     * @param string $siteId
     * @param int    $count
     *
     * @dataProvider providePathSiteIdAndCount
     */
    public function testFindByIncludedPathAndSiteId($path, $siteId, $count)
    {
        $nodes = $this->repository->findByIncludedPathAndSiteId($path, $siteId);

        $this->assertGreaterThanOrEqual($count, count($nodes));
    }

    /**
     * @return array
     */
    public function providePathSiteIdAndCount()
    {
        return array(
            array('root', '2', 5),
            array('root/fixture_page_community', '2', 0),
            array('transverse', '2', 0),
        );
    }

    /**
     * @param string $siteId
     * @param int    $nodeNumber
     * @param int    $version
     *
     * @dataProvider provideSiteIdAndNumberOfNode
     */
    public function testFindLastVersionByType($siteId, $nodeNumber, $version)
    {
        $nodes = $this->repository->findLastVersionByType($siteId);

        $this->assertCount($nodeNumber, $nodes);
        $this->assertSameNode('fr', $version, $siteId, $nodes[NodeInterface::ROOT_NODE_ID]);
    }

    /**
     * @return array
     */
    public function provideSiteIdAndNumberOfNode()
    {
        return array(
            array('2', 5, 2),
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
        $this->assertSame(false, $node->isDeleted());
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
        $nodes = $this->repository->getFooterTree($language, $siteId);
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
            array('2', 1, 1, 'fr', 'fixture_page_legal_mentions'),
            array('2', 1, 1, 'en'),
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
        $nodes = $this->repository->getMenuTree($language, $siteId);

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
            array('2', 4, 1, 'fr'),
            array('2', 4, 1, 'en'),
        );
    }

    /**
     * @param string $nodeId
     * @param int    $nbLevel
     * @param int    $nodeNumber
     * @param int    $version
     * @param string $siteId
     * @param string $local
     *
     * @dataProvider provideForGetSubMenu
     */
    public function testGetSubMenu($nodeId, $nbLevel, $nodeNumber, $version, $siteId, $local)
    {
        $nodes = $this->repository->getSubMenu($nodeId, $nbLevel, $local, $siteId);

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
            array(NodeInterface::ROOT_NODE_ID, 1, 5, 1, '2', 'fr'),
            array(NodeInterface::ROOT_NODE_ID, 1, 5, 1, '2', 'en'),
        );
    }

    /**
     * @param string $language
     * @param string $siteId
     * @param int    $count
     *
     * @dataProvider provideLanguageSiteIdAndCount
     */
    public function testFindLastPublishedVersion($language, $siteId, $count)
    {
        $nodes = $this->repository->findLastPublishedVersion($language, $siteId);

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
            array('en', '2', 5),
            array('fr', '2', 5),
        );
    }


    /**
     * @param string       $author
     * @param boolean|null $published
     * @param int          $count
     *
     * @dataProvider provideContributor
     */
    public function testFindByAuthor($author, $published, $count)
    {
        $this->assertCount($count, $this->repository->findByAuthor($author, $published));
    }

    /**
     * @return array
     */
    public function provideContributor()
    {
        return array(
            array('fake_admin', null, 3),
            array('fake_admin', false, 1),
            array('fake_admin', true, 2),
            array('fakeContributor', false, 0),
            array('fakeContributor', null, 0),
        );
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param int    $count
     *
     * @dataProvider provideFindPublishedSortedVersionData
     */
    public function testFindPublishedSortedByVersion($nodeId, $language, $count)
    {
        $this->assertCount($count, $this->repository->findPublishedSortedByVersion($nodeId, $language, '2'));
    }

    /**
     * @return array
     */
    public function provideFindPublishedSortedVersionData()
    {
        return array(
            array(NodeInterface::ROOT_NODE_ID, 'fr', 1),
            array(NodeInterface::ROOT_NODE_ID, 'en', 1),
            array('fixture_page_contact', 'en', 1),
        );
    }

    /**
     * @param string $language
     *
     * @dataProvider provideLanguage
     */
    public function testFindSubTreeByPath($language)
    {
        $nodes = $this->repository->findSubTreeByPath('root', '2', $language);

        $this->assertCount(4, $nodes);
    }

    /**
     * @return array
     */
    public function provideLanguage()
    {
        return array(
            array('en'),
            array('fr'),
        );
    }

    /**
     * @param string $parentId
     * @param string $routePattern
     * @param string $nodeId
     *
     * @dataProvider provideParentRouteAndNodeId
     */
    public function testFindByParentAndRoutePattern($parentId, $routePattern, $nodeId)
    {
        $this->assertEmpty($this->repository->findByParentAndRoutePattern($parentId, $routePattern, $nodeId, '2'));
    }

    /**
     * @return array
     */
    public function provideParentRouteAndNodeId()
    {
        return array(
            array(NodeInterface::ROOT_NODE_ID, 'page-contact', 'fixture_page_contact'),
            array(NodeInterface::ROOT_NODE_ID, 'mentions-legales', 'fixture_page_legal_mentions'),
        );
    }

    /**
     * @param string $type
     * @param int    $count
     *
     * @dataProvider provideNodeTypeAndCount
     */
    public function testFindAllNodesOfTypeInLastPublishedVersionForSite($type, $count)
    {
        $this->assertCount($count, $this->repository->findAllNodesOfTypeInLastPublishedVersionForSite($type, '2'));
    }

    /**
     * @return array
     */
    public function provideNodeTypeAndCount()
    {
        return array(
            array(NodeInterface::TYPE_DEFAULT, 10),
            array(NodeInterface::TYPE_ERROR, 0),
            array(NodeInterface::TYPE_TRANSVERSE, 0),
        );
    }
}

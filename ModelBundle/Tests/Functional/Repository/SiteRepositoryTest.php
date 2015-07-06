<?php

namespace OpenOrchestra\ModelBundle\Tests\Functional\Repository;

use OpenOrchestra\ModelBundle\Repository\SiteRepository;
use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class SiteRepositoryTest
 */
class SiteRepositoryTest extends KernelTestCase
{
    /**
     * @var SiteRepository
     */
    protected $repository;

    /**
     * Set up test
     */
    protected function setUp()
    {
        parent::setUp();

        static::bootKernel();
        $this->repository = static::$kernel->getContainer()->get('open_orchestra_model.repository.site');
    }

    /**
     * @param boolean $deleted
     * @param array   $columns
     * @param array   $descriptionEntity
     * @param string  $search
     * @param int     $skip
     * @param int     $limit
     * @param integer $count
     * 
     * @dataProvider provideDeletedAndPaginateAndSearch
     */
    public function testFindByDeletedForPaginateAndSearch($deleted, $descriptionEntity, $columns, $search, $skip, $limit, $count)
    {
        $configuration = PaginateFinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);
        $configuration->setPaginateConfiguration(null, $skip, $limit);
        $sites = $this->repository->findByDeletedForPaginate($deleted, $configuration);
        $this->assertCount($count, $sites);
    }

    /**
     * @return array
     */
    public function provideDeletedAndPaginateAndSearch()
    {
        $descriptionEntity = $this->getDescriptionColumnEntity();

        return array(
            array(false, array(), array(), null, 0 ,2 , 2),
            array(false, array(), array(), null, 0 ,1 , 1),
            array(true, array(), array(), null, 0 ,2 , 1),
            array(false, $descriptionEntity, $this->generateColumnsProvider('2'), 'demo', null, null, 1),
            array(false, $descriptionEntity, $this->generateColumnsProvider('1'), 'demo', null, null, 0),
            array(false, $descriptionEntity, $this->generateColumnsProvider('1', 'demo'), null, null, null, 0),
            array(false, $descriptionEntity, $this->generateColumnsProvider('1', 'front'), null, null, null, 1),
            array(false, $descriptionEntity, $this->generateColumnsProvider(), 'fake search', null, null, 0)
        );
    }

    /**
     * @param array   $order
     * @param array   $orderId
     *
     * @dataProvider provideOrderDeletedAndPaginateAndSearch
     */
    public function testOrderFindByDeletedForPaginateAndSearch($order, $orderId)
    {
        $configuration = PaginateFinderConfiguration::generateFromVariable(
            $this->getDescriptionColumnEntity(),
            $this->generateColumnsProvider('', 'site'));
        $configuration->setPaginateConfiguration($order);
        $sites = $this->repository->findByDeletedForPaginate(false, $configuration);

        $this->assertSameOrder($sites, $orderId);
    }

    /**
     * @return array
     */
    public function provideOrderDeletedAndPaginateAndSearch()
    {
        return array(
            array(array(array('column' => 0,'dir' => 'desc')), array(2, 1)),
            array(array(array('column' => 0,'dir' => 'asc')), array(1, 2)),
            array(array(array('column' => 1,'dir' => 'asc')), array(2, 1)),
            array(array(array('column' => 1,'dir' => 'desc')), array(1, 2)),
        );
    }

    /**
     * @param boolean $deleted
     * @param integer $count
     *
     * @dataProvider provideBooleanDeletedCount
     */
    public function testCountByDeleted($deleted, $count)
    {
        $sites = $this->repository->countByDeleted($deleted);
        $this->assertEquals($count, $sites);
    }

    /**
     * @return array
     */
    public function provideBooleanDeletedCount()
    {
        return array(
            array(true, 1),
        );
    }

    /**
     * @param boolean $deleted
     * @param array   $descriptionEntity
     * @param array   $columns
     * @param string  $search
     * @param int     $count
     *
     * @dataProvider provideColumnsAndSearchAndCount
     */
    public function testCountByDeletedWithSearchFilter($deleted, $descriptionEntity, $columns, $search, $count)
    {
        $configuration = FinderConfiguration::generateFromVariable($descriptionEntity, $columns, $search);
        $sites = $this->repository->countWithSearchFilterByDeleted($deleted, $configuration);
        $this->assertEquals($count, $sites);
    }

    /**
     * @return array
     */
    public function provideColumnsAndSearchAndCount()
    {
        $descriptionEntity = $this->getDescriptionColumnEntity();

        return array(
            array(false, $descriptionEntity, $this->generateColumnsProvider('2'), 'demo', 1),
            array(false, $descriptionEntity, $this->generateColumnsProvider('1'), 'demo', 0),
            array(false, $descriptionEntity, $this->generateColumnsProvider('1', 'demo'), null, 0),
            array(false, $descriptionEntity, $this->generateColumnsProvider('1', 'front'), null, 1),
            array(true, $descriptionEntity, $this->generateColumnsProvider(), 'fake search', 0)
        );
    }

    /**
     * Generate columns of site with search value
     *
     * @param string $searchSiteId
     * @param string $searchName
     *
     * @return array
     */
    protected function generateColumnsProvider($searchSiteId = '', $searchName = '')
    {
        return array(
            array('name' => 'site_id', 'searchable' => true, 'orderable' => true, 'search' => array('value' => $searchSiteId)),
            array('name' => 'name', 'searchable' => true, 'orderable' => true, 'search' => array('value' => $searchName)),
        );
    }

    /**
     * Generate relation between columns names and entities attributes
     *
     * @return array
     */
    protected function getDescriptionColumnEntity()
    {
        return array(
            'site_id' => array('key' => 'siteId'),
            'name' => array('key' => 'name'),
        );
    }

    /**
     * @param array $sites
     * @param array $orderId
     */
    protected function assertSameOrder($sites, $orderId)
    {
        foreach ($sites as $index => $site) {
            $this->assertEquals($site->getSiteId(), $orderId[$index]);
        }
    }
}

<?php

namespace OpenOrchestra\ModelBundle\Tests\Functional\Repository;

use OpenOrchestra\ModelBundle\Repository\RouteDocumentRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Test RouteDocumentRepositoryTest
 */
class RouteDocumentRepositoryTest extends KernelTestCase
{
    /**
     * @var RouteDocumentRepository
     */
    protected $repository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        static::bootKernel();
        $this->repository = static::$kernel->getContainer()->get('open_orchestra_model.repository.route_document');
    }

    /**
     * Test simple route
     *
     * @param string $pathInfo
     * @param string $name
     *
     * @dataProvider provideSimplePathInfo
     */
    public function testFindByPathInfoWithSingleAnswer($pathInfo, $name)
    {
        $routes = $this->repository->findByPathInfo($pathInfo);

        $this->assertCount(1, $routes);
        $this->assertSame($name, $routes[0]->getName());
    }

    /**
     * @return array
     */
    public function provideSimplePathInfo()
    {
        return array(
            array('foo', 'foo'),
            array('baz/bar', 'baz/bar'),
            array('foo/test', 'foo/{bar}'),
            array('foo/test/baz', 'foo/{bar}/baz'),
        );
    }

    /**
     * @param string $pathInfo
     * @param string $name0
     * @param string $name1
     *
     * @dataProvider provideMultiplePath
     */
    public function testFindByPathInfoWithMultipleAnswer($pathInfo, $name0, $name1)
    {
        $routes = $this->repository->findByPathInfo($pathInfo);

        $this->assertCount(2, $routes);
        $this->assertSame($name0, $routes[0]->getName());
        $this->assertSame($name1, $routes[1]->getName());
    }

    /**
     * @return array
     */
    public function provideMultiplePath()
    {
        return array(
            array('foo/bar', 'foo/bar', 'foo/{bar}'),
        );
    }
}

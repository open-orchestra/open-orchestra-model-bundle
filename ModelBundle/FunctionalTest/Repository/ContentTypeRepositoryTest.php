<?php

namespace PHPOrchestra\ModelBundle\FunctionalTest\Repository;

use PHPOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ContentTypeRepositoryTest
 */
class ContentTypeRepositoryTest extends KernelTestCase
{
    /**
     * @var ContentTypeRepositoryInterface
     */
    protected $repository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        static::bootKernel();
        $this->repository = static::$kernel->getContainer()->get('php_orchestra_model.repository.content_type');
    }

    /**
     * Test find one with no version
     */
    public function testFindOneByContentTypeIdAndVersionWithNoVersion()
    {
        $contentType = $this->repository->findOneByContentTypeIdAndVersion('car');

        $this->assertGreaterThanOrEqual(3, $contentType->getVersion());
    }

    /**
     * Test find one with version
     *
     * @param int $version
     *
     * @dataProvider provideCarVersion
     */
    public function testFindOneByContentTypeIdAndVersionWithVersion($version)
    {
        $contentType = $this->repository->findOneByContentTypeIdAndVersion('car', $version);

        $this->assertEquals($version, $contentType->getVersion());
    }

    /**
     * @return array
     */
    public function provideCarVersion()
    {
        return array(
            array(2),
            array(3),
        );
    }
}

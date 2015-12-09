<?php

namespace OpenOrchestra\ModelBundle\Tests\Functional\Repository;

use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ContentTypeRepositoryTest
 *
 * @group integrationTest
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
        $this->repository = static::$kernel->getContainer()->get('open_orchestra_model.repository.content_type');
    }

    /**
     * @param string $contentType
     * @param int    $version
     *
     * @dataProvider provideContentTypeAndVersionNumber
     */
    public function testFindOneByContentTypeIdInLastVersion($contentType, $version)
    {
        $contentTypeElement = $this->repository->findOneByContentTypeIdInLastVersion($contentType);

        $this->assertEquals($version, $contentTypeElement->getVersion());
    }

    /**
     * @return array
     */
    public function provideContentTypeAndVersionNumber()
    {
        return array(
            array('car', 2),
            array('customer', 1),
        );
    }
}

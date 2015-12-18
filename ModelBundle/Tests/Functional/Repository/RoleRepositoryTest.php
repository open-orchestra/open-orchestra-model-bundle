<?php

namespace OpenOrchestra\ModelBundle\Tests\Functional\Repository;

use OpenOrchestra\ModelBundle\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class RoleRepositoryTest
 */
class RoleRepositoryTest extends KernelTestCase
{
    /**
     * @var RoleRepository
     */
    protected $repository;

    /**
     * Set up test
     */
    protected function setUp()
    {
        parent::setUp();

        static::bootKernel();
        $this->repository = static::$kernel->getContainer()->get('open_orchestra_model.repository.role');
    }

    /**
     * Test has statused element
     */
    public function testHasStatusedElement()
    {
        $statusRepository = static::$kernel->getContainer()->get('open_orchestra_model.repository.status');
        $status = $statusRepository->findOneByInitial();

        $this->assertTrue($this->repository->hasStatusedElement($status));
    }
}

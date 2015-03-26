<?php

namespace OpenOrchestra\ModelBundle\Tests\Functional\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class FieldAutoGenerableRepositoryInterfaceTest
 */
class FieldAutoGenerableRepositoryInterfaceTest extends KernelTestCase
{
    /**
     * Set up test
     */
    protected function setUp()
    {
        parent::setUp();

        static::bootKernel();
    }

    /**
     * @param string $serviceName
     *
     * @dataProvider provideServiceName
     */
    public function testImplementFieldAutoGenerableRepositoryInterface($serviceName)
    {
        $repository = static::$kernel->getContainer()->get($serviceName);

        $this->assertInstanceOf('OpenOrchestra\ModelBundle\Repository\FieldAutoGenerableRepositoryInterface', $repository);
    }

    /**
     * @return array
     */
    public function provideServiceName()
    {
        return array(
            array('open_orchestra_model.repository.node'),
            array('open_orchestra_model.repository.template'),
            array('open_orchestra_model.repository.content'),
        );
    }
}

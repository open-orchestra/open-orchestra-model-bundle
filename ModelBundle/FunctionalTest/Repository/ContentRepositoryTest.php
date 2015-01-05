<?php

namespace PHPOrchestra\ModelBundle\FunctionalTest\Repository;

use PHPOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ContentRepositoryTest
 */
class ContentRepositoryTest extends KernelTestCase
{
    /**
     * @var ContentRepositoryInterface
     */
    protected $repository;

    /**
     * Set up test
     */
    protected function setUp()
    {
        parent::setUp();

        static::bootKernel();
        $this->repository = static::$kernel->getContainer()->get('php_orchestra_model.repository.content');
    }

    /**
     * @param string $keywords
     * @param int    $count
     *
     * @dataProvider provideKeywordAndCount
     */
    public function testFindByKeywords($keywords, $count)
    {
        $keywordsElement = $this->repository->findByKeywords($keywords);

        $this->assertCount($count, $keywordsElement);
    }

    /**
     * @return array
     */
    public function provideKeywordAndCount()
    {
        return array(
            array('Lorem', 2),
            array('Sit', 4),
            array('Dolor', 0),
            array('Lorem,Sit', 5),
        );
    }
}

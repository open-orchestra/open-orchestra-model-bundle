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
     * @param string      $contentType
     * @param string|null $keywords
     * @param int         $count
     *
     * @dataProvider provideContentTypeKeywordAndCount
     */
    public function testFindByContentTypeAndKeywords($contentType = '', $keywords = null, $count)
    {
        $element = $this->repository->findByContentTypeAndKeywords($contentType, $keywords);

        $this->assertCount($count, $element);
    }

    /**
     * @return array
     */
    public function provideContentTypeKeywordAndCount()
    {
        return array(
            array('car', 'Lorem', 1),
            array('car', 'Sit', 0),
            array('car', 'Dolor', 0),
            array('car', 'Lorem,Sit', 1),
            array('news', 'Lorem', 0),
            array('news', 'Sit', 3),
            array('news', 'Dolor', 0),
            array('news', 'Lorem,Sit', 3),
            array('news', null, 5),
            array('car', null, 1),
            array('', null, 7),
            array('', 'Lorem', 2),
            array('', 'Sit', 4),
            array('', 'Dolor', 0),
            array('', 'Lorem,Sit', 5),
        );
    }
}

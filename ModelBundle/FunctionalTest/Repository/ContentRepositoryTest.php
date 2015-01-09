<?php

namespace PHPOrchestra\ModelBundle\FunctionalTest\Repository;

use Phake;
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

        $this->currentSiteManager = Phake::mock('PHPOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        Phake::when($this->currentSiteManager)->getCurrentSiteId()->thenReturn('1');
        Phake::when($this->currentSiteManager)->getCurrentSiteDefaultLanguage()->thenReturn('fr');

        static::bootKernel();
        $this->repository = static::$kernel->getContainer()->get('php_orchestra_model.repository.content');
        $this->repository->setCurrentSiteManager($this->currentSiteManager);
    }

    /**
     * Test find all news
     */
    public function testFindAllNews()
    {
        $elements = $this->repository->findAllNews();

        $this->assertCount(0, $elements);
    }

    /**
     * @param string      $name
     * @param boolean     $exists
     * @param int         $count
     *
     * @dataProvider provideTestUnicityInContext
     */
    public function testTestUnicityInContext($name, $exists)
    {
        $test = $this->repository->testUnicityInContext($name);

        $this->assertEquals($exists, $test);

    }

    /**
     * @return array
     */
    public function provideTestUnicityInContext()
    {
        return array(
            array('Welcome', true),
            array('fakeContentId', false),
        );
    }

    /**
     * @param string $contentId
     *
     * @dataProvider provideFindOneByContentId
     */
    public function testFindOneByContentId($contentId)
    {
        $content = $this->repository->findOneByContentId($contentId);
        $this->assertSameContent(null, null, null, $contentId, $content);
        $this->assertEquals($contentId, $content->getContentId());
    }

    /**
     * @return array
     */
    public function provideFindOneByContentId()
    {
        return array(
            array('echonext'),
            array('bien_vivre_en_france'),
        );
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
        $elements = $this->repository->findByContentTypeAndKeywords($contentType, $keywords);

        $this->assertCount($count, $elements);
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
            array('news', null, 1755),
            array('car', null, 1),
            array('', null, 1757),
            array('', 'Lorem', 2),
            array('', 'Sit', 4),
            array('', 'Dolor', 0),
            array('', 'Lorem,Sit', 5),
        );
    }

    /**
     * @param string $contentType
     * @param string $language
     *
     * @dataProvider provideFindOneByContentIdAndLanguage
     */
    public function testFindOneByContentIdAndLanguage($contentId, $language)
    {
        $content = $this->repository->findOneByContentIdAndLanguage($contentId, $language);

        $this->assertSameContent($language, null, null, $contentId, $content);
    }

    /**
     * @return array
     */
    public function provideFindOneByContentIdAndLanguage()
    {
        return array(
            array('echonext', 'fr'),
            array('bien_vivre_en_france', 'fr'),
        );
    }

    /**
     * @param string $contentType
     * @param string $language
     *
     * @dataProvider provideFindByContentIdAndLanguage
     */
    public function testFindByContentIdAndLanguage($contentId, $language = null)
    {
        $contents = $this->repository->findByContentIdAndLanguage($contentId, $language);

        foreach($contents as $content){
            $this->assertSameContent($language, null, null, $contentId, $content);
        }

    }

    /**
     * @return array
     */
    public function provideFindByContentIdAndLanguage()
    {
        return array(
            array('echonext', 'fr'),
            array('bien_vivre_en_france', 'fr'),
        );
    }

    /**
     * @param string $contentType
     * @param string $language
     * @param int    $version
     *
     * @dataProvider provideFindOneByContentIdAndLanguageAndVersion
     */
    public function testFindOneByContentIdAndLanguageAndVersion($contentId, $language, $version)
    {
        $content = $this->repository->findOneByContentIdAndLanguageAndVersion($contentId, $language, $version);

        $this->assertSameContent($language, $version, null, $contentId, $content);

    }

    /**
     * @return array
     */
    public function provideFindOneByContentIdAndLanguageAndVersion()
    {
        return array(
            array('echonext', 'fr', 1),
            array('bien_vivre_en_france', 'fr', 1),
        );
    }

    /**
     * @param string        $language
     * @param int           $version
     * @param string        $siteId
     * @param ContentInterface $content
     * @param string        $contentId
     */
    protected function assertSameContent($language, $version, $siteId, $contentId, $content)
    {
        $this->assertInstanceOf('PHPOrchestra\ModelInterface\Model\ContentInterface', $content);
        $this->assertSame($contentId, $content->getContentId());
        if (!is_null($language)) {
            $this->assertSame($language, $content->getLanguage());
        }
        if (!is_null($version)) {
            $this->assertSame($version, $content->getVersion());
        }
        if (!is_null($siteId)) {
            $this->assertSame($siteId, $content->getSiteId());
        }
        $this->assertSame(false, $content->getDeleted());
    }
}

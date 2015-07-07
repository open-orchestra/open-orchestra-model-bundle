<?php

namespace OpenOrchestra\ModelBundle\Tests\Functional\Repository;

use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Phake;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
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

    protected $currentSiteManager;

    /**
     * Set up test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->currentSiteManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        Phake::when($this->currentSiteManager)->getCurrentSiteId()->thenReturn('1');
        Phake::when($this->currentSiteManager)->getCurrentSiteDefaultLanguage()->thenReturn('fr');

        static::bootKernel();
        $this->repository = static::$kernel->getContainer()->get('open_orchestra_model.repository.content');
    }

    /**
     * @param string  $name
     * @param boolean $exists
     *
     * @dataProvider provideTestUniquenessInContext
     */
    public function testTestUniquenessInContext($name, $exists)
    {
        $test = $this->repository->testUniquenessInContext($name);

        $this->assertEquals($exists, $test);

    }

    /**
     * @return array
     */
    public function provideTestUniquenessInContext()
    {
        return array(
            array('welcome', true),
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
            array('notre_vision'),
            array('bien_vivre_en_france'),
        );
    }

    /**
     * @param $contentId
     * @param $version
     * @param string|null $language
     *
     * @dataProvider providefindLastPublishedVersionByContentIdAndLanguage
     */
    public function testFindLastPublishedVersionByContentIdAndLanguage($contentId, $version, $language)
    {
        $content = $this->repository->findOneByContentId($contentId, $language);
        $this->assertSameContent($language, $version, null, $contentId, $content);
        $this->assertEquals($contentId, $content->getContentId());
    }

    /**
     * @return array
     */
    public function providefindLastPublishedVersionByContentIdAndLanguage()
    {
        return array(
            array('notre_vision', 1, 'fr'),
            array('bien_vivre_en_france', 1, 'fr'),
        );
    }

    /**
     * @param string      $contentType
     * @param string      $choiceType
     * @param string|null $keywords
     * @param int         $count
     *
     * @dataProvider provideContentTypeKeywordAndCount
     */
    public function testFindByContentTypeAndChoiceTypeAndKeywords($contentType = '', $choiceType, $keywords = null, $count)
    {
        $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
        $elements = $this->repository->findByContentTypeAndChoiceTypeAndKeywordsAndLanguage($language, $contentType, $choiceType, $keywords);

        $this->assertCount($count, $elements);
    }

    /**
     * @return array
     */
    public function provideContentTypeKeywordAndCount()
    {
        return array(
            array('car', ContentRepositoryInterface::CHOICE_AND, 'Lorem', 3),
            array('car', ContentRepositoryInterface::CHOICE_AND, 'Sit', 1),
            array('car', ContentRepositoryInterface::CHOICE_AND, 'Dolor', 0),
            array('car', ContentRepositoryInterface::CHOICE_AND, 'Lorem,Sit', 1),
            array('news', ContentRepositoryInterface::CHOICE_AND, 'Lorem', 1),
            array('news', ContentRepositoryInterface::CHOICE_AND, 'Sit', 2),
            array('news', ContentRepositoryInterface::CHOICE_AND, 'Dolor', 0),
            array('news', ContentRepositoryInterface::CHOICE_AND, 'Lorem,Sit', 1),
            array('news', ContentRepositoryInterface::CHOICE_AND, '', 254),
            array('car', ContentRepositoryInterface::CHOICE_AND, '', 3),
            array('', ContentRepositoryInterface::CHOICE_AND, null, 258),
            array('', ContentRepositoryInterface::CHOICE_AND, '', 258),
            array('', ContentRepositoryInterface::CHOICE_AND, 'Lorem', 5),
            array('', ContentRepositoryInterface::CHOICE_AND, 'Sit', 4),
            array('', ContentRepositoryInterface::CHOICE_AND, 'Dolor', 0),
            array('', ContentRepositoryInterface::CHOICE_AND, 'Lorem,Sit', 3),
            array('car', ContentRepositoryInterface::CHOICE_OR, 'Lorem', 5),
            array('car', ContentRepositoryInterface::CHOICE_OR, 'Sit', 6),
            array('car', ContentRepositoryInterface::CHOICE_OR, 'Dolor', 3),
            array('car', ContentRepositoryInterface::CHOICE_OR, 'Lorem,Sit', 5),
            array('news', ContentRepositoryInterface::CHOICE_OR, 'Lorem', 258),
            array('news', ContentRepositoryInterface::CHOICE_OR, 'Sit', 256),
            array('news', ContentRepositoryInterface::CHOICE_OR, 'Dolor', 254),
            array('news', ContentRepositoryInterface::CHOICE_OR, 'Lorem,Sit', 256),
            array('news', ContentRepositoryInterface::CHOICE_OR, '', 254),
            array('car', ContentRepositoryInterface::CHOICE_OR, null, 3),
            array('', ContentRepositoryInterface::CHOICE_OR, null, 258),
            array('', ContentRepositoryInterface::CHOICE_OR, 'Lorem', 5),
            array('', ContentRepositoryInterface::CHOICE_OR, 'Sit', 4),
            array('', ContentRepositoryInterface::CHOICE_OR, 'Dolor', 0),
            array('', ContentRepositoryInterface::CHOICE_OR, 'Lorem,Sit', 3),
            array('', ContentRepositoryInterface::CHOICE_OR, '', 258),
        );
    }

    /**
     * @param string $contentId
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
            array('notre_vision', 'fr'),
            array('bien_vivre_en_france', 'fr'),
        );
    }

    /**
     * @param string $contentId
     * @param string $language
     *
     * @dataProvider provideFindByContentIdAndLanguage
     */
    public function testFindByContentIdAndLanguage($contentId, $language)
    {
        $contents = $this->repository->findByContentIdAndLanguage($contentId, $language);

        foreach ($contents as $content) {
            $this->assertSameContent($language, null, null, $contentId, $content);
        }

    }

    /**
     * @return array
     */
    public function provideFindByContentIdAndLanguage()
    {
        return array(
            array('notre_vision', 'fr'),
            array('bien_vivre_en_france', 'fr'),
        );
    }

    /**
     * @param string $contentId
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
            array('notre_vision', 'fr', 1),
            array('bien_vivre_en_france', 'fr', 1),
        );
    }

    /**
     * @param string   $contentType
     * @param array    $descriptionEntity
     * @param array    $search
     * @param string   $siteId
     * @param int      $skip
     * @param int      $limit
     * @param integer  $count
     *
     * @dataProvider provideContentTypeAndPaginateAndSearchAndSiteId
     */
    public function testFindByContentTypeAndSiteIdInLastVersionForPaginate($contentType, $descriptionEntity, $search, $siteId, $skip, $limit, $count)
    {
        $configuration = PaginateFinderConfiguration::generateFromVariable($descriptionEntity, $search);
        $configuration->setPaginateConfiguration(null, $skip, $limit);
        $contents = $this->repository->findByContentTypeAndSiteIdInLastVersionForPaginate($contentType, $configuration, $siteId);
        $this->assertCount($count, $contents);
    }

    /**
     * @return array
     */
    public function provideContentTypeAndPaginateAndSearchAndSiteId()
    {
        $descriptionEntity = $this->getDescriptionColumnEntity();

        return array(
            array('car', $descriptionEntity, null, null, 0 ,5 , 3),
            array('car', $descriptionEntity, null, null, 0 ,1 , 1),
            array('car', $descriptionEntity, $this->generateColumnsProvider(array('name' => '206')), null, 0 ,2 , 1),
            array('car', $descriptionEntity, $this->generateColumnsProvider(array('version' => '2')), null, 0 ,2 , 2),
            array('news', $descriptionEntity, null, null, 0 , 100, 100),
            array('news', $descriptionEntity, null, null, 50 , 100, 100),
            array('news', $descriptionEntity, $this->generateColumnsProvider(array('name' => 'news')), null, 0 , null, 250),
            array('car', $descriptionEntity, null, '1', 0 ,5 , 2),
            array('car', $descriptionEntity, null, '2', 0 ,5 , 3),
        );
    }

    /**
     * @param string  $contentType
     * @param integer $count
     *
     * @dataProvider provideContentTypeCount
     */
    public function testCountByContentTypeInLastVersion($contentType, $count)
    {
        $contents = $this->repository->countByContentTypeInLastVersion($contentType);
        $this->assertEquals($count, $contents);
    }

    /**
     * @return array
     */
    public function provideContentTypeCount()
    {
        return array(
            array('car', 3),
            array('customer', 1),
            array('news', 254),
        );
    }

    /**
     * @param string  $contentType
     * @param array   $descriptionEntity
     * @param string  $search
     * @param int     $count
     *
     * @dataProvider provideColumnsAndSearchAndCount
     */
    public function testCountByContentTypeInLastVersionWithSearchFilter($contentType, $descriptionEntity, $search, $count)
    {
        $configuration = FinderConfiguration::generateFromVariable($descriptionEntity, $search);

        $sites = $this->repository->countByContentTypeInLastVersionWithFilter($contentType, $configuration);
        $this->assertEquals($count, $sites);
    }

    /**
     * @return array
     */
    public function provideColumnsAndSearchAndCount(){
        $descriptionEntity = $this->getDescriptionColumnEntity();

        return array(
            array('car', $descriptionEntity, $this->generateColumnsProvider(array('name' => '206')), 1),
            array('car', $descriptionEntity, $this->generateColumnsProvider(null, 'portes'), 2),
            array('news', $descriptionEntity, $this->generateColumnsProvider(null, 'news'), 250)
        );
    }

    /**
     * Generate columns of content with search value
     *
     * @param array|null $searchColumns
     * @param string     $globalSearch
     *
     * @return array
     */
    protected function generateColumnsProvider($searchColumns = null, $globalSearch = '')
    {
        $search = array();
        if (null !== $searchColumns) {
            $columns = array();
            foreach ($searchColumns as $name => $value) {
                $columns[$name] = $value;
            }
            $search['columns'] = $columns;
        }

        if (!empty($globalSearch)) {
            $search['global'] = $globalSearch;
        }

        return $search;
    }

    /**
     * Generate relation between columns names and entities attributes
     *
     * @return array
     */
    protected function getDescriptionColumnEntity()
    {
        return array(
            'name'         => array('key' => 'name'),
            'status_label' => array('key' => 'status.name'),
            'version'      => array('key' => 'version' , 'type' => 'integer'),
            'language'     => array('key' => 'language'),
        );
    }

    /**
     * @param string                                               $language
     * @param int                                                  $version
     * @param string                                               $siteId
     * @param \OpenOrchestra\ModelInterface\Model\ContentInterface $content
     * @param string                                               $contentId
     */
    protected function assertSameContent($language, $version, $siteId, $contentId, $content)
    {
        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\ContentInterface', $content);
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

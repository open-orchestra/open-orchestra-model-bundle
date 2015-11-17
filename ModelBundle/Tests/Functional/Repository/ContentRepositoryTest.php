<?php

namespace OpenOrchestra\ModelBundle\Tests\Functional\Repository;

use OpenOrchestra\Pagination\Configuration\FinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Phake;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OpenOrchestra\Mapping\Annotations\Search;

/**
 * Class ContentRepositoryTest
 *
 * @group integrationTest
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
        Phake::when($this->currentSiteManager)->getCurrentSiteId()->thenReturn('2');
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
     * @dataProvider providefindLastPublishedVersion
     */
    public function testFindLastPublishedVersion($contentId, $version, $language)
    {
        $content = $this->repository->findLastPublishedVersion($contentId, $language);
        $this->assertSameContent($language, $version, null, $contentId, $content);
        $this->assertEquals($contentId, $content->getContentId());
    }

    /**
     * @return array
     */
    public function providefindLastPublishedVersion()
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
    public function testFindByContentTypeAndKeywords($contentType = '', $choiceType, $keywords = null, $count)
    {
        $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
        $elements = $this->repository->findByContentTypeAndKeywords($language, $contentType, $choiceType, $keywords);

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
            array('news', ContentRepositoryInterface::CHOICE_AND, '', 4),
            array('car', ContentRepositoryInterface::CHOICE_AND, '', 3),
            array('', ContentRepositoryInterface::CHOICE_AND, null, 9),
            array('', ContentRepositoryInterface::CHOICE_AND, '', 9),
            array('', ContentRepositoryInterface::CHOICE_AND, 'Lorem', 5),
            array('', ContentRepositoryInterface::CHOICE_AND, 'Sit', 4),
            array('', ContentRepositoryInterface::CHOICE_AND, 'Dolor', 0),
            array('', ContentRepositoryInterface::CHOICE_AND, 'Lorem,Sit', 3),
            array('car', ContentRepositoryInterface::CHOICE_OR, 'Lorem', 5),
            array('car', ContentRepositoryInterface::CHOICE_OR, 'Sit', 6),
            array('car', ContentRepositoryInterface::CHOICE_OR, 'Dolor', 3),
            array('car', ContentRepositoryInterface::CHOICE_OR, 'Lorem,Sit', 5),
            array('news', ContentRepositoryInterface::CHOICE_OR, 'Lorem', 8),
            array('news', ContentRepositoryInterface::CHOICE_OR, 'Sit', 6),
            array('news', ContentRepositoryInterface::CHOICE_OR, 'Dolor', 4),
            array('news', ContentRepositoryInterface::CHOICE_OR, 'Lorem,Sit', 6),
            array('news', ContentRepositoryInterface::CHOICE_OR, '', 4),
            array('car', ContentRepositoryInterface::CHOICE_OR, null, 3),
            array('', ContentRepositoryInterface::CHOICE_OR, null, 9),
            array('', ContentRepositoryInterface::CHOICE_OR, 'Lorem', 5),
            array('', ContentRepositoryInterface::CHOICE_OR, 'Sit', 4),
            array('', ContentRepositoryInterface::CHOICE_OR, 'Dolor', 0),
            array('', ContentRepositoryInterface::CHOICE_OR, 'Lorem,Sit', 3),
            array('', ContentRepositoryInterface::CHOICE_OR, '', 9),
        );
    }

    /**
     * @param string $contentId
     * @param string $language
     *
     * @dataProvider provideFindOneByContentIdAndLanguage
     */
    public function testFindOneByLanguage($contentId, $language)
    {
        $content = $this->repository->findOneByLanguage($contentId, $language);

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
    public function testFindByLanguage($contentId, $language)
    {
        $contents = $this->repository->findByLanguage($contentId, $language);

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
    public function testFindOneByLanguageAndVersion($contentId, $language, $version)
    {
        $content = $this->repository->findOneByLanguageAndVersion($contentId, $language, $version);

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
    public function testFindPaginatedLastVersionByContentTypeAndSite($contentType, $descriptionEntity, $search, $order, $siteId, $skip, $limit, $count, $name = null)
    {
        $configuration = PaginateFinderConfiguration::generateFromVariable($descriptionEntity, $search);
        $configuration->setPaginateConfiguration($order, $skip, $limit);
        $contents = $this->repository->findPaginatedLastVersionByContentTypeAndSite($contentType, $configuration, $siteId);

        if(!is_null($name)) {
            $this->assertEquals($name, $contents[0]->getName());
        }
        $this->assertCount($count, $contents);
    }

    /**
     * @return array
     */
    public function provideContentTypeAndPaginateAndSearchAndSiteId()
    {
        $descriptionEntity = $this->getDescriptionColumnEntity();

        return array(
            array('car', $descriptionEntity, null, array("name" => "name", "dir" => "asc"), null, 0 ,5 , 3, '206 3 portes en'),
            array('car', $descriptionEntity, null, array("name" => "name", "dir" => "desc"), null, 0 ,5 , 3, 'R5 3 portes en'),
            array('car', $descriptionEntity, null, array("name" => "attributes.car_name.string_value", "dir" => "asc"), null, 0 ,5 , 3, '206 3 portes en'),
            array('car', $descriptionEntity, null, array("name" => "attributes.car_name.string_value", "dir" => "desc"), null, 0 ,5 , 3, 'R5 3 portes en'),
            array('car', $descriptionEntity, null, null, null, 0 ,1 , 1),
            array('car', $descriptionEntity, $this->generateColumnsProvider(array('name' => '206')), null, null, 0 ,2 , 1),
            array('car', $descriptionEntity, $this->generateColumnsProvider(array('version' => '2')), null, null, 0 ,2 , 2),
            array('news', $descriptionEntity, null, null, null, 0 , 100, 4),
            array('news', $descriptionEntity, null, null, null, 50 , 100, 0),
            array('news', $descriptionEntity, $this->generateColumnsProvider(array('name' => 'news')), null, null, 0 , null, 0),
            array('car', $descriptionEntity, null, null, '2', 0 ,5 , 3),
            array('car', $descriptionEntity, $this->generateColumnsProvider(array('status_label' => 'publish')), null, null, null ,null , 3),
            array('car', $descriptionEntity, $this->generateColumnsProvider(array('status_label' => 'publié')), null, null, null ,null , 3),
            array('car', $descriptionEntity, $this->generateColumnsProvider(array('status_label' => 'draft')), null, null, null ,null , 0),
            array('car', $descriptionEntity, $this->generateColumnsProvider(array('status_label' => 'brouillon')), null, null, null ,null , 0),

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
            array('customer', 2),
            array('news', 4),
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
            array('news', $descriptionEntity, $this->generateColumnsProvider(null, 'news'), 4)
        );
    }

    /**
     * @param string       $author
     * @param boolean|null $published
     * @param int          $count
     *
     * @dataProvider provideFindByAuthor
     */
    public function testFindByAuthor($author, $published, $count)
    {
        $contents = $this->repository->findByAuthor($author, $published);
        $this->assertCount($count, $contents);
    }

    /**
     * @return array
     */
    public function provideFindByAuthor()
    {
        return array(
            array('admin', null, 7),
            array('admin', false, 0),
            array('admin', true, 7),
            array('fakeContributor', false, 0),
            array('fakeContributor', null, 0),
        );
    }

    /**
     * @param string       $author
     * @param string       $siteId
     * @param boolean|null $published
     * @param int          $count
     *
     * @dataProvider provideFindByAuthorAndSiteId
     */
    public function testFindByAuthorAndSiteId($author, $siteId, $published, $count)
    {
        $contents = $this->repository->findByAuthorAndSiteId($author, $siteId, $published);
        $this->assertCount($count, $contents);
    }

    /**
     * @return array
     */
    public function provideFindByAuthorAndSiteId()
    {
        return array(
            array('admin', '2', null, 6),
            array('admin', '2', false, 0),
            array('admin', '2', true, 6),
            array('fakeContributor', '2', false, 0),
            array('fakeContributor', '2', null, 0),
            array('admin', '3', true, 5),
            array('admin', 'not-an-id', true, 4)
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
        return array (
            'name' =>
            array (
                'key' => 'name',
                'field' => 'name',
                'type' => 'string',
            ),
            'language' =>
            array (
                'key' => 'language',
                'field' => 'language',
                'type' => 'string',
            ),
            'status_label' =>
            array (
                'key' => 'status_label',
                'field' => 'status',
                'type' => 'translatedValue',
            ),
            'version' =>
            array (
                'key' => 'version',
                'field' => 'version',
                'type' => 'integer',
            ),
            'linked_to_site' =>
            array (
                'key' => 'linked_to_site',
                'field' => 'linkedToSite',
                'type' => 'boolean',
            ),
            'created_by' =>
            array (
                'key' => 'created_by',
                'field' => 'createdBy',
                'type' => 'string',
            ),
            'updated_by' =>
            array (
                'key' => 'updated_by',
                'field' => 'updatedBy',
                'type' => 'string',
            ),
            'created_at' =>
            array (
                'key' => 'created_at',
                'field' => 'createdAt',
                'type' => 'date',
            ),
            'updated_at' =>
            array (
                'key' => 'updated_at',
                'field' => 'updatedAt',
                'type' => 'date',
            ),
            'attributes.car_name.string_value' =>
            array(
                'key' => 'attributes.car_name.string_value',
                'field' => 'attributes.car_name.stringValue',
                'type' => 'string',
                'value' => NULL,
            ),
            'attributes.description.string_value' =>
            array(
                'key' => 'attributes.description.string_value',
                'field' => 'attributes.description.stringValue',
                'type' => 'string',
                'value' => NULL,
            ),
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
        $this->assertSame(false, $content->isDeleted());
    }
}

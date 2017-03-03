<?php

namespace OpenOrchestra\ModelBundle\DependencyInjection;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelBundle\DependencyInjection\OpenOrchestraModelExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class OpenOrchestraModelExtensionTest
 */
class OpenOrchestraModelExtensionTest extends AbstractBaseTestCase
{

    /**
     * Test default config
     */
    public function testDefaultConfig()
    {
        $container = $this->loadContainerFromFile("empty");

        $this->assertSame(array("Doctrine\\Common\\DataFixtures\\FixtureInterface"), $container->getParameter('open_orchestra_model.fixtures_interface.all'));
        $this->assertSame(array("OpenOrchestra\\ModelInterface\\DataFixtures\\OrchestraProductionFixturesInterface"), $container->getParameter('open_orchestra_model.fixtures_interface.production'));
        $this->assertSame(array("OpenOrchestra\\ModelInterface\\DataFixtures\\OrchestraFunctionalFixturesInterface"), $container->getParameter('open_orchestra_model.fixtures_interface.functional'));
        $this->assertSame(array("all", "production", "functional"), $container->getParameter('open_orchestra_model.fixtures.command'));
        $this->assertSame(array('linkedToSite', 'deleted'), $container->getParameter('open_orchestra_model.content.immutable_properties'));
    }

    /**
     * @param string     $file
     * @param string     $class
     * @param string     $name
     * @param string     $repository
     * @param bool|false $filter
     *
     * @dataProvider provideDocumentClass
     */
    public function testConfigDocument($file, $class, $name, $repository = null , $filter = false)
    {
        $container = $this->loadContainerFromFile($file);
        $this->assertEquals($class, $container->getParameter('open_orchestra_model.document.' . $name . '.class'));
        if (null !== $repository) {
            $this->assertDefinition($container->getDefinition('open_orchestra_model.repository.' . $name), $class, $repository, $filter);
        }
    }

    /**
     * @return array
     */
    public function provideDocumentClass()
    {
        return array(
            '1'  => array("empty", "OpenOrchestra\\ModelBundle\\Document\\Content"          , "content"           , "OpenOrchestra\\ModelBundle\\Repository\\ContentRepository"         , true),
            '2'  => array("empty", "OpenOrchestra\\ModelBundle\\Document\\ContentAttribute" , "content_attribute"                                                                             ),
            '3'  => array("empty", "OpenOrchestra\\ModelBundle\\Document\\ContentType"      , "content_type"      , "OpenOrchestra\\ModelBundle\\Repository\\ContentTypeRepository"           ),
            '4'  => array("empty", "OpenOrchestra\\ModelBundle\\Document\\Node"             , "node"              , "OpenOrchestra\\ModelBundle\\Repository\\NodeRepository"                  ),
            '5'  => array("empty", "OpenOrchestra\\ModelBundle\\Document\\Area"             , "area"                                                                                          ),
            '6'  => array("empty", "OpenOrchestra\\ModelBundle\\Document\\Block"            , "block"                                                                                         ),
            '7'  => array("empty", "OpenOrchestra\\ModelBundle\\Document\\Site"             , "site"              , "OpenOrchestra\\ModelBundle\\Repository\\SiteRepository"                  ),
            '8'  => array("empty", "OpenOrchestra\\ModelBundle\\Document\\RouteDocument"    , "route_document"    , "OpenOrchestra\\ModelBundle\\Repository\\RouteDocumentRepository"         ),
            '9'  => array("empty", "OpenOrchestra\\ModelBundle\\Document\\SiteAlias"        , "site_alias"                                                                                    ),
            '10' => array("empty", "OpenOrchestra\\ModelBundle\\Document\\FieldOption"      , "field_option"                                                                                  ),
            '11' => array("empty", "OpenOrchestra\\ModelBundle\\Document\\FieldType"        , "field_type"                                                                                    ),
            '12' => array("empty", "OpenOrchestra\\ModelBundle\\Document\\Status"           , "status"            , "OpenOrchestra\\ModelBundle\\Repository\\StatusRepository"                ),
            '13' => array("empty", "OpenOrchestra\\ModelBundle\\Document\\EmbedStatus"      , "embed_status"                                                                                  ),
            '15' => array("empty", "OpenOrchestra\\ModelBundle\\Document\\Redirection"      , "redirection"       , "OpenOrchestra\\ModelBundle\\Repository\\RedirectionRepository"           ),
            '16' => array("empty", "OpenOrchestra\\ModelBundle\\Document\\Keyword"          , "keyword"           , "OpenOrchestra\\ModelBundle\\Repository\\KeywordRepository"               ),
            '17' => array("empty", "OpenOrchestra\\ModelBundle\\Document\\TranslatedValue"  , "translated_value"                                                                              ),
            '18' => array("empty", "OpenOrchestra\\ModelBundle\\Document\\TrashItem"        , "trash_item"        , "OpenOrchestra\\ModelBundle\\Repository\\TrashItemRepository"             ),
            '19' => array("value", "FakeContent"                                            , "content"           , "FakeContentRepository"                                                   ),
            '20' => array("value", "FakeContentType"                                        , "content_type"      , "FakeContentTypeRepository"                                               ),
            '21' => array("value", "FakeNode"                                               , "node"              , "FakeNodeRepository"                                                      ),
            '22' => array("value", "FakeSite"                                               , "site"              , "FakeSiteRepository"                                                      ),
            '23' => array("value", "FakeRouteDocument"                                      , "route_document"    , "FakeRouteDocumentRepository"                                             ),
            '24' => array("value", "FakeBlock"                                              , "block"                                                                                         ),
            '25' => array("value", "FakeBlock"                                              , "block"                                                                                         ),
            '26' => array("value", "FakeSiteAlias"                                          , "site_alias"                                                                                    ),
            '27' => array("value", "FakeContentAttribute"                                   , "content_attribute"                                                                             ),
            '28' => array("value", "FakeFieldOption"                                        , "field_option"                                                                                  ),
            '29' => array("value", "FakeFieldType"                                          , "field_type"                                                                                    ),
            '30' => array("value", "FakeEmbedStatus"                                        , "embed_status"                                                                                  ),
            '31' => array("value", "FakeTranslatedValue"                                    , "translated_value"                                                                              ),
            '33' => array("value", "FakeRedirection"                                        , "redirection"       , "FakeRedirectionRepository"                                               ),
            '34' => array("value", "FakeTrashItem"                                          , "trash_item"        , "FakeTrashItemRepository"                                                 ),
            '35' => array("value", "FakeKeyword"                                            , "keyword"           , "FakeKeywordRepository"                                                   ),
            '36' => array("empty", "OpenOrchestra\\ModelBundle\\Document\\Reference"        , "reference"                                                                                     ),
            '37' => array("value", "FakeClassReference"                                     , "reference"                                                                                     )
        );
    }


    /**
     * Test config with value
     */
    public function testConfigWithValue()
    {
        $container = $this->loadContainerFromFile("value");

        $this->assertSame(array('FakeAllFixtureInterface'), $container->getParameter('open_orchestra_model.fixtures_interface.all'));
        $this->assertSame(array('fake1', 'fake2', 'linkedToSite', 'deleted'), $container->getParameter('open_orchestra_model.content.immutable_properties'));
    }


    /**
     * @param Definition $definition
     * @param string     $class
     * @param string     $repository
     * @param bool       $filterType
     */
    private function assertDefinition(Definition $definition, $class, $repository, $filterType)
    {
        $this->assertSame($definition->getClass(), $repository);
        $factory = $definition->getFactory();
        $this->assertSame($factory[1], "getRepository");
        $this->assertSame($definition->getArgument(0), $class);
        $this->assertTrue($definition->hasMethodCall('setAggregationQueryBuilder'));
        if ($filterType) {
            $this->assertTrue($definition->hasMethodCall('setFilterTypeManager'));
        }
    }
    /**
     * @param string $file
     *
     * @return ContainerBuilder
     */
    private function loadContainerFromFile($file)
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', false);
        $container->setParameter('kernel.cache_dir', '/tmp');
        $container->registerExtension(new OpenOrchestraModelExtension());

        $locator = new FileLocator(__DIR__ . '/Fixtures/config/');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load($file . '.yml');
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}

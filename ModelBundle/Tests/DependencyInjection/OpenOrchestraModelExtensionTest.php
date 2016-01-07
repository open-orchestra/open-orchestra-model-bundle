<?php

namespace OpenOrchestra\BaseApiBundle\DependencyInjection;

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
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\Content", "content", "OpenOrchestra\\ModelBundle\\Repository\\ContentRepository", true),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\ContentAttribute", "content_attribute"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\ContentType", "content_type", "OpenOrchestra\\ModelBundle\\Repository\\ContentTypeRepository", true),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\Node", "node", "OpenOrchestra\\ModelBundle\\Repository\\NodeRepository"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\Area", "area"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\Block", "block"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\Site", "site", "OpenOrchestra\\ModelBundle\\Repository\\SiteRepository", true),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\RouteDocument", "route_document", "OpenOrchestra\\ModelBundle\\Repository\\RouteDocumentRepository"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\SiteAlias", "site_alias"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\Template", "template", "OpenOrchestra\\ModelBundle\\Repository\\TemplateRepository"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\FieldOption", "field_option"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\FieldType", "field_type"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\Status", "status", "OpenOrchestra\\ModelBundle\\Repository\\StatusRepository", true),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\EmbedStatus", "embed_status"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\Theme", "theme", "OpenOrchestra\\ModelBundle\\Repository\\ThemeRepository"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\Role", "role", "OpenOrchestra\\ModelBundle\\Repository\\RoleRepository", true),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\Redirection", "redirection", "OpenOrchestra\\ModelBundle\\Repository\\RedirectionRepository", true),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\Keyword", "keyword", "OpenOrchestra\\ModelBundle\\Repository\\KeywordRepository", true),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\EmbedKeyword", "embed_keyword"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\TranslatedValue", "translated_value"),
            array("empty", "OpenOrchestra\\ModelBundle\\Document\\TrashItem", "trash_item", "OpenOrchestra\\ModelBundle\\Repository\\TrashItemRepository", true),
            array("value", "FakeContent", "content", "FakeContentRepository"),
            array("value", "FakeContentType", "content_type", "FakeContentTypeRepository"),
            array("value", "FakeNode", "node", "FakeNodeRepository"),
            array("value", "FakeSite", "site", "FakeSiteRepository"),
            array("value", "FakeRouteDocument", "route_document", "FakeRouteDocumentRepository"),
            array("value", "FakeTemplate", "template", "FakeTemplateRepository"),
            array("value", "FakeBlock", "block"),
            array("value", "FakeBlock", "block"),
            array("value", "FakeSiteAlias", "site_alias"),
            array("value", "FakeContentAttribute", "content_attribute"),
            array("value", "FakeFieldOption", "field_option"),
            array("value", "FakeFieldType", "field_type"),
            array("value", "FakeEmbedStatus", "embed_status"),
            array("value", "FakeTranslatedValue", "translated_value"),
            array("value", "FakeEmbedKeyword", "embed_keyword"),
            array("value", "FakeTheme", "theme", "FakeThemeRepository"),
            array("value", "FakeRole", "role", "FakeRoleRepository"),
            array("value", "FakeRedirection", "redirection", "FakeRedirectionRepository"),
            array("value", "FakeTrashItem", "trash_item", "FakeTrashItemRepository"),
            array("value", "FakeKeyword", "keyword", "FakeKeywordRepository"),
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

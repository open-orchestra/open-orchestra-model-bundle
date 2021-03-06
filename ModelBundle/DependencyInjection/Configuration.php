<?php

namespace OpenOrchestra\ModelBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class
 * }
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('open_orchestra_model');

        $rootNode->children()
            ->arrayNode('content_immutable_properties')
                ->info('Immutable properties of the content class')
                ->prototype('scalar')->end()
            ->end()
            ->arrayNode('fixtures_interface')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('all')
                        ->prototype('scalar')->end()
                        ->defaultValue(array('Doctrine\Common\DataFixtures\FixtureInterface'))
                    ->end()
                    ->arrayNode('production')
                        ->prototype('scalar')->end()
                        ->defaultValue(array('OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface'))
                    ->end()
                    ->arrayNode('functional')
                        ->prototype('scalar')->end()
                        ->defaultValue(array('OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface'))
                    ->end()
                ->end()
            ->end()
            ->arrayNode('document')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('content')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\Content')->end()
                            ->scalarNode('repository')->defaultValue('OpenOrchestra\ModelBundle\Repository\ContentRepository')->end()
                        ->end()
                    ->end()
                    ->arrayNode('content_attribute')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\ContentAttribute')->end()
                        ->end()
                    ->end()
                    ->arrayNode('content_type')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\ContentType')->end()
                            ->scalarNode('repository')->defaultValue('OpenOrchestra\ModelBundle\Repository\ContentTypeRepository')->end()
                        ->end()
                    ->end()
                    ->arrayNode('node')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\Node')->end()
                            ->scalarNode('repository')->defaultValue('OpenOrchestra\ModelBundle\Repository\NodeRepository')->end()
                        ->end()
                    ->end()
                    ->arrayNode('area')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\Area')->end()
                        ->end()
                    ->end()
                    ->arrayNode('block')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\Block')->end()
                            ->scalarNode('repository')->defaultValue('OpenOrchestra\ModelBundle\Repository\BlockRepository')->end()
                            ->end()
                    ->end()
                    ->arrayNode('site')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\Site')->end()
                            ->scalarNode('repository')->defaultValue('OpenOrchestra\ModelBundle\Repository\SiteRepository')->end()
                        ->end()
                    ->end()
                    ->arrayNode('route_document')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\RouteDocument')->end()
                            ->scalarNode('repository')->defaultValue('OpenOrchestra\ModelBundle\Repository\RouteDocumentRepository')->end()
                        ->end()
                    ->end()
                    ->arrayNode('site_alias')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\SiteAlias')->end()
                        ->end()
                    ->end()
                    ->arrayNode('field_option')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\FieldOption')->end()
                        ->end()
                    ->end()
                    ->arrayNode('field_type')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\FieldType')->end()
                        ->end()
                    ->end()
                    ->arrayNode('status')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\Status')->end()
                            ->scalarNode('repository')->defaultValue('OpenOrchestra\ModelBundle\Repository\StatusRepository')->end()
                        ->end()
                    ->end()
                    ->arrayNode('embed_status')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\EmbedStatus')->end()
                        ->end()
                    ->end()
                    ->arrayNode('redirection')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\Redirection')->end()
                            ->scalarNode('repository')->defaultValue('OpenOrchestra\ModelBundle\Repository\RedirectionRepository')->end()
                        ->end()
                    ->end()
                    ->arrayNode('keyword')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\Keyword')->end()
                            ->scalarNode('repository')->defaultValue('OpenOrchestra\ModelBundle\Repository\KeywordRepository')->end()
                        ->end()
                    ->end()
                    ->arrayNode('translated_value')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\TranslatedValue')->end()
                        ->end()
                    ->end()
                    ->arrayNode('trash_item')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\TrashItem')->end()
                            ->scalarNode('repository')->defaultValue('OpenOrchestra\ModelBundle\Repository\TrashItemRepository')->end()
                        ->end()
                    ->end()
                    ->arrayNode('history')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\History')->end()
                        ->end()
                    ->end()
                    ->arrayNode('workflow_profile')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\WorkflowProfile')->end()
                            ->scalarNode('repository')->defaultValue('OpenOrchestra\ModelBundle\Repository\WorkflowProfileRepository')->end()
                        ->end()
                    ->end()
                    ->arrayNode('workflow_profile_collection')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\WorkflowProfileCollection')->end()
                        ->end()
                    ->end()
                    ->arrayNode('workflow_transition')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\WorkflowTransition')->end()
                        ->end()
                    ->end()
                    ->arrayNode('authorization')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\Authorization')->end()
                        ->end()
                    ->end()
                    ->arrayNode('reference')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue('OpenOrchestra\ModelBundle\Document\Reference')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}

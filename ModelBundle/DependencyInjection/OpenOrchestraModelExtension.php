<?php

namespace OpenOrchestra\ModelBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpenOrchestraModelExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $immutableProperties = array_merge($config['content_immutable_properties'], array('linkedToSite', 'deleted'));
        $container->setParameter('open_orchestra_model.content.immutable_properties', $immutableProperties);
        $container->setParameter('open_orchestra_model.production_fixtures_interface', $config['production_fixtures_interface']);
        foreach ($config['document'] as $class => $content) {
            if (is_array($content)) {
                $container->setParameter('open_orchestra_model.document.' . $class . '.class', $content['class']);
                if (array_key_exists('repository', $content)) {
                    $definition = new Definition($content['repository'], array($content['class']));
                    $definition->setFactory(array(new Reference('doctrine.odm.mongodb.document_manager'), 'getRepository'));
                    $definition->addMethodCall('setAggregationQueryBuilder', array(
                        new Reference('doctrine_mongodb.odm.default_aggregation_query')
                    ));
                    if (method_exists($content['repository'],'setFilterTypeManager')) {
                        $definition->addMethodCall('setFilterTypeManager', array(
                            new Reference('open_orchestra_pagination.filter_type.manager')
                        ));
                    }
                    $container->setDefinition('open_orchestra_model.repository.' . $class, $definition);
                }
            }
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('listener.yml');
        $loader->load('services.yml');
        $loader->load('validator.yml');
        $loader->load('manager.yml');
        $loader->load('form.yml');
        $loader->load('transformer.yml');
        $loader->load('helper.yml');
    }
}

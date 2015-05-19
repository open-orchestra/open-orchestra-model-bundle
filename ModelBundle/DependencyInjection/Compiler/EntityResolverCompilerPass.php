<?php

namespace OpenOrchestra\ModelBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class EntityResolverCompilerPass
 */
class EntityResolverCompilerPass implements  CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $defaultResolveDocument = array(
            'OpenOrchestra\ModelInterface\Model\TranslatedValueInterface' => 'OpenOrchestra\ModelBundle\Document\TranslatedValue',
            'OpenOrchestra\ModelInterface\Model\ReadSiteInterface' => 'OpenOrchestra\ModelBundle\Document\Site',
            'FOS\UserBundle\Model\GroupInterface' => 'OpenOrchestra\GroupBundle\Document\Group',
            'Symfony\Component\Security\Core\User\UserInterface' => 'OpenOrchestra\UserBundle\Document\User',
            'OpenOrchestra\UserBundle\Model\ApiClientInterface' => 'OpenOrchestra\UserBundle\Document\ApiClient',
            'OpenOrchestra\ModelInterface\Model\EmbedStatusInterface' => 'OpenOrchestra\ModelBundle\Document\EmbedStatus',
            'OpenOrchestra\ModelInterface\Model\RoleInterface' => 'OpenOrchestra\ModelBundle\Document\Role',
            'OpenOrchestra\ModelInterface\Model\AreaInterface' => 'OpenOrchestra\ModelBundle\Document\Area',
            'OpenOrchestra\ModelInterface\Model\BlockInterface' => 'OpenOrchestra\ModelBundle\Document\Block',
            'OpenOrchestra\ModelInterface\Model\StatusInterface' => 'OpenOrchestra\ModelBundle\Document\Status',
            'OpenOrchestra\ModelInterface\Model\ThemeInterface' => 'OpenOrchestra\ModelBundle\Document\Theme',
            'OpenOrchestra\ModelInterface\Model\SiteAliasInterface' => 'OpenOrchestra\ModelBundle\Document\SiteAlias',
            'OpenOrchestra\ModelInterface\Model\ContentAttributeInterface' => 'OpenOrchestra\ModelBundle\Document\ContentAttribute',
            'OpenOrchestra\ModelInterface\Model\FieldTypeInterface' => 'OpenOrchestra\ModelBundle\Document\FieldType',
            'OpenOrchestra\ModelInterface\Model\FieldOptionInterface' => 'OpenOrchestra\ModelBundle\Document\FieldOption'
        );

        $definition = $container->findDefinition('doctrine_mongodb.odm.listeners.resolve_target_document');
        $definitionCalls = $definition->getMethodCalls();
        foreach($defaultResolveDocument as $interface => $class) {
            if (! $this->resolverExist($definitionCalls, $interface)) {
                $definition->addMethodCall('addResolveTargetDocument', array($interface, $class, array()));
            }
        }
        if (!$definition->hasTag('doctrine_mongodb.odm.event_listener')) {
            $definition->addTag('doctrine_mongodb.odm.event_listener', array('event' => 'preLoad'));
        }
    }

    /**
     * Check if model interface are already resolved
     *
     * @param $methodCalls
     * @param $interface
     *
     * @return bool
     */
    protected function resolverExist($methodCalls, $interface) {
        foreach ($methodCalls as $call) {
            if ($call[0] === 'addResolveTargetDocument' && $call[1][0] === $interface) {
                return true;
            }
        }

        return false;
    }
}

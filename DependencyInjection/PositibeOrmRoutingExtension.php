<?php

namespace Positibe\Bundle\OrmRoutingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PositibeOrmRoutingExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');
        $loader->load('auto_routing_services.yml');

        $autoRouting = array();
        foreach ($config['auto_routing'] as $key => $autoRoutingConfig) {
            $autoRouting[$key] = $autoRoutingConfig;
        }

        $container->getDefinition('positibe_orm_routing_auto.metadata.factory.builder')
            ->addArgument(array(array('path' => $autoRouting, 'type' => 'parameters')));

        $routeBuilder = $container->getDefinition('positibe_orm_routing.route_factory');
        $availableControllers = array();
        foreach ($config['controllers'] as $name => $controllersConfig) {
            $routeBuilder->addMethodCall('addController', array($name, $controllersConfig['_controller']));
            $availableControllers[] = $name;
        }
        $container->setParameter('positibe_orm_content.available_controllers', $availableControllers);

        $this->addClassesToCompile(array(
                'Symfony\\Cmf\\Bundle\\RoutingBundle\\Routing\DynamicRouter',
                'Symfony\\Cmf\\Bundle\\RoutingBundle\\Doctrine\\Orm\\RouteProvider',
                'Symfony\\Cmf\\Component\\Routing\\NestedMatcher\\NestedMatcher',
                'Symfony\\Cmf\\Component\\Routing\\ContentAwareGenerator',
                'Symfony\\Cmf\\Component\\Routing\\NestedMatcher\\UrlMatcher',
                'Symfony\\Cmf\\Component\\Routing\\Enhancer\\RouteContentEnhancer',
                'Symfony\\Cmf\\Component\\Routing\\Enhancer\\FieldPresenceEnhancer',
                'Symfony\\Cmf\\Component\\Routing\\Enhancer\\FieldPresenceEnhancer',
                'Symfony\\Cmf\\Component\\Routing\\Enhancer\\FieldMapEnhancer',
                'Symfony\\Cmf\\Component\\Routing\\Enhancer\\FieldByClassEnhancer',
                'Positibe\\Bundle\\OrmRoutingBundle\\Routing\\Enhancer\\RouteContentEnhancer'
            ));
    }

}

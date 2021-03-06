<?php

namespace Positibe\Bundle\CmfRoutingExtraBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PositibeCmfRoutingExtraExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yml');

        if (isset($container->getParameter('kernel.bundles')['LuneticsLocaleBundle'])) {
            $loader->load('translatable_enhancer.yml');
            $this->addAnnotatedClassesToCompile(['Positibe\\Bundle\\CmfRoutingExtraBundle\\Routing\\TranslatableEnhancer']);
        }

        $routeBuilder = $container->getDefinition('positibe_routing.route_factory');
        $availableControllers = [];
        foreach ($config['controllers'] as $name => $controllersConfig) {
            $routeBuilder->addMethodCall('addController', array($name, $controllersConfig['_controller']));
            $availableControllers[] = $name;
        }
        $container->setParameter('positibe_cms.available_controllers', $availableControllers);

        $availableTemplates = [];
        foreach ($config['templates'] as $name => $template) {
            $routeBuilder->addMethodCall('addTemplate', array($name, $template));
            $availableTemplates[] = $name;
        }
        $container->setParameter('positibe_cms.available_templates', $availableTemplates);

        if ($nameFilterRegex = $config['name_filter_regex']) {
            $container->getDefinition('positibe_routing.content_aware_generator')->addMethodCall(
                'setNameFilterRegex',
                [$nameFilterRegex]
            );
            $container->setAlias('cmf_routing.generator', new Alias('positibe_routing.content_aware_generator', false));
        }

        $this->addAnnotatedClassesToCompile(
            [
                'Symfony\\Cmf\\Bundle\\RoutingBundle\\Routing\DynamicRouter',
                'Symfony\\Cmf\\Bundle\\RoutingBundle\\Doctrine\\Orm\\RouteProvider',
                'Symfony\\Cmf\\Component\\Routing\\ChainRouter',
                'Symfony\\Cmf\\Component\\Routing\\DynamicRouter',
                'Symfony\\Cmf\\Component\\Routing\\NestedMatcher\\NestedMatcher',
                'Symfony\\Cmf\\Component\\Routing\\ContentAwareGenerator',
                'Symfony\\Cmf\\Component\\Routing\\NestedMatcher\\UrlMatcher',
                'Symfony\\Cmf\\Component\\Routing\\Enhancer\\RouteContentEnhancer',
                'Symfony\\Cmf\\Component\\Routing\\Enhancer\\FieldPresenceEnhancer',
                'Symfony\\Cmf\\Component\\Routing\\Enhancer\\FieldMapEnhancer',
                'Symfony\\Cmf\\Component\\Routing\\Enhancer\\FieldByClassEnhancer',
                'Symfony\\Cmf\\Component\\Routing\\Enhancer\\ContentRepositoryEnhancer',
                'Positibe\\Bundle\\CmfRoutingExtraBundle\\Routing\\TranslatableEnhancer',
                'Positibe\\Bundle\\CmfRoutingExtraBundle\\Routing\\ContentAwareGenerator',
            ]
        );
    }

}

<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmRoutingBundle\AutoRouting;

use Metadata\MetadataFactoryInterface;
use Positibe\Bundle\OrmRoutingBundle\AutoRouting\Adapter\OrmAdapter;
use Positibe\Bundle\OrmRoutingBundle\Entity\Route;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
use Symfony\Cmf\Component\RoutingAuto\ConflictResolverInterface;
use Symfony\Cmf\Component\RoutingAuto\ServiceRegistry;
use Symfony\Cmf\Component\RoutingAuto\UriContext;
use Symfony\Cmf\Component\RoutingAuto\UriContextCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class AutoRouteManager
 * @package Positibe\Bundle\OrmRoutingBundle\AutoRouting
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class AutoRouteManager
{
    private $defaultLocale;
    private $metadataFactory;
    private $serviceRegistry;
    private $conflictResolver;
    private $adapter;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        ServiceRegistry $serviceRegistry,
        OrmAdapter $adapter,
        ConflictResolverInterface $conflictResolver,
        $defaultLocale
    ) {
        $this->defaultLocale = $defaultLocale;
        $this->metadataFactory = $metadataFactory;
        $this->serviceRegistry = $serviceRegistry;
        $this->adapter = $adapter;
        $this->conflictResolver = $conflictResolver;
    }

    /**
     * @param $entity
     * @return bool
     */
    public function needNewRoute(RouteReferrersInterface $entity)
    {
        $currentLocale = $this->getLocale($entity);
        if (count($entity->getRoutes())) {
            /** @var Route $route */
            foreach ($entity->getRoutes()->toArray() as $route) {
                $routeLocale = $route->getLocale();
                if (empty($routeLocale)) {
                    $route->setLocale($this->defaultLocale);
                    $routeLocale = $this->defaultLocale;
                }
                if ($route instanceof Route && $routeLocale === $currentLocale) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param $entity
     * @return mixed
     */
    private function getLocale($entity)
    {
        $locale = method_exists(
            $entity,
            'getLocale'
        ) ? $entity->getLocale() : $this->defaultLocale;

        return !empty($locale) ? $locale : $this->defaultLocale;
    }

    /**
     * @param UriContextCollection $uriContextCollection
     */
    public function buildUriContextCollection(UriContextCollection $uriContextCollection)
    {
        $this->getUriContextsForDocument($uriContextCollection);

        foreach ($uriContextCollection->getUriContexts() as $uriContext) {
            $existingRoute = $this->adapter->findRouteForUri($uriContext->getUri());

            $route = null;

            if ($existingRoute) {
                $uri = $this->resolveConflict($uriContext);
                $uriContext->setUri($uri);
            }

            if (!$route) {
                $route = $this->adapter->createRoute(
                    $uriContext->getUri(),
                    $uriContext->getSubjectObject(),
                    $uriContext->getLocale()
                );

            }
            $uriContext->setAutoRoute($route);
        }

//        $this->pendingUriContextCollections[] = $uriContextCollection;
    }

    /**
     * Populates an empty UriContextCollection with UriContexts
     *
     * @param $uriContextCollection UriContextCollection
     */
    private function getUriContextsForDocument(UriContextCollection $uriContextCollection)
    {
        // create and add uri context to stack
        $uriContext = $uriContextCollection->createUriContext(
            $this->getLocale($uriContextCollection->getSubjectObject())
        );
        $uriContextCollection->addUriContext($uriContext);

        // generate the URL
        $uri = $this->generateUri($uriContext);

        // update the context with the URL
        $uriContext->setUri($uri);
    }

    /**
     * @param UriContext $uriContext
     * @return string
     */
    public function generateUri(UriContext $uriContext)
    {
        $realClassName = get_class($uriContext->getSubjectObject());
        $metadata = $this->metadataFactory->getMetadataForClass($realClassName);

        $tokenProviderConfigs = $metadata->getTokenProviders();

        $tokens = array();
        foreach ($tokenProviderConfigs as $name => $options) {
            $tokenProvider = $this->serviceRegistry->getTokenProvider($options['name']);

            // I can see the utility of making this a singleton, but it is a massive
            // code smell to have this in a base class and be also part of the interface
            $optionsResolver = new OptionsResolver();
            $tokenProvider->configureOptions($optionsResolver);

            $tokens['{' . $name . '}'] = $tokenProvider->provideValue(
                $uriContext,
                $optionsResolver->resolve($options['options'])
            );
        }

        $uriSchema = $metadata->getUriSchema();
        $uri = strtr($uriSchema, $tokens);

        return $uri;
    }

    /**
     * {@inheritDoc}
     */
    public function resolveConflict(UriContext $uriContext)
    {
        $uri = $this->conflictResolver->resolveConflict($uriContext);

        return $uri;
    }
}
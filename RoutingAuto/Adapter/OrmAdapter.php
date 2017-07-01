<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\CmfRoutingExtraBundle\RoutingAuto\Adapter;

use Doctrine\ORM\EntityManager;
use Positibe\Bundle\CmfRoutingExtraBundle\Factory\RouteFactory;
use Positibe\Bundle\CmfRoutingExtraBundle\Entity\AutoRoute;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Model\Route;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
use Symfony\Cmf\Component\RoutingAuto\AdapterInterface;
use Symfony\Cmf\Component\RoutingAuto\Model\AutoRouteInterface;
use Symfony\Cmf\Component\RoutingAuto\UriContext;


/**
 * Class OrmAdapter
 * @package Positibe\Bundle\CmfRoutingExtraBundle\RoutingAuto\Adapter
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class OrmAdapter implements AdapterInterface
{
    const TAG_NO_MULTILANG = 'no-multilang';

    private $em;
    private $routeFactory;
    private $defaultLocale;
    private $locales;

    public function __construct(EntityManager $entityManager, RouteFactory $routeFactory, $defaultLocale, $locales)
    {
        $this->em = $entityManager;
        $this->routeFactory = $routeFactory;
        $this->defaultLocale = $defaultLocale;
        $this->locales = $locales;
    }

    /**
     * Return locales for object
     *
     * @param $object
     * @return array|void
     */
    public function getLocales($object)
    {
        if ($object instanceof TranslatableInterface) {
            return [$object->getLocale() ?: $this->defaultLocale];
        }

        return [$this->defaultLocale];
    }

    /**
     * Translate the given object into the given locale
     *
     * @param object $object
     * @param string $locale e.g. fr, en, de, be, etc.
     *
     * @return object The translated subject object
     */
    public function translateObject($object, $locale)
    {
//        if (method_exists($object, 'setLocale')) {
//            $object->setLocale($locale);
//            $this->em->refresh($object);
//        }

        return $object;
    }

    /**
     * Create a new auto route at the given path
     * with the given document as the content.
     *
     * @param UriContext $uriContext
     * @param object|RouteReferrersInterface $entity
     * @param string $tag
     *
     * @return AutoRouteInterface new route document
     */
    public function createAutoRoute(UriContext $uriContext, $entity, $tag)
    {
        $route = $this->routeFactory->createContentAutoRoute(
            $uriContext->getUri(),
            $entity,
            null,
            $uriContext->getLocale(),
            $uriContext->getDefaults()
        );

        $entity->addRoute($route);

        return $route;
    }

    /**
     * Return the canonical name for the given class, this is
     * required as somethimes an ORM may return a proxy class.
     *
     * @param $className
     * @return string|void
     */
    public function getRealClassName($className)
    {
        return $className;
    }

    /**
     * Compares the locale the route is matching and the given locale.
     *
     * @param AutoRouteInterface $autoRoute
     * @param string|null $locale
     *
     * @return bool True when the locales are equal, false otherwize
     */
    public function compareAutoRouteLocale(AutoRouteInterface $autoRoute, $locale)
    {
        return $autoRoute->getLocale() === $locale;
    }

    /**
     * Return true if the content associated with the auto route
     * and the given content object are the same.
     *
     * @param AutoRouteInterface $autoRoute
     * @param $contentObject
     *
     * @return bool True when the contents are equal, false otherwise
     */
    public function compareAutoRouteContent(AutoRouteInterface $autoRoute, $contentObject)
    {
        if ($autoRoute->getContent() === $contentObject) {
            return true;
        }

        return false;
    }

    public function compareRouteContent(Route $route, $contentObject)
    {
        if ($route->getContent() === $contentObject) {
            return true;
        }

        return false;
    }

    /**
     * Attempt to find a route with the given URL
     *
     * @param string $uri
     * @param UriContext $uriContext
     *
     * @return null|Route
     */
    public function findRouteForUri($uri, UriContext $uriContext)
    {
        if ($route = $this->em->getRepository('PositibeCmfRoutingExtraBundle:AutoRoute')->findOneBy(
            ['staticPrefix' => $uri]
        )
        ) {
            return $route;
        }

        return null;
    }

    /**
     * Generate a tag which can be used to identify this route from
     * other routes as required.
     *
     * @param UriContext $uriContext
     *
     * @return string
     */
    public function generateAutoRouteTag(UriContext $uriContext)
    {
        return $uriContext->getLocale() ?: self::TAG_NO_MULTILANG;
    }

    /**
     * Migrate the descendant path elements from one route to another.
     *
     * e.g. in an RDBMS with a routes:
     *
     *    /my-blog
     *    /my-blog/posts/post1
     *    /my-blog/posts/post2
     *    /my-new-blog
     *
     * We want to migrate the children of "my-blog" to "my-new-blog" so that
     * we have:
     *
     *    /my-blog
     *    /my-new-blog
     *    /my-new-blog/posts/post1
     *    /my-new-blog/posts/post2
     *
     * @param AutoRouteInterface $srcAutoRoute
     * @param AutoRouteInterface $destAutoRoute
     */
    public function migrateAutoRouteChildren(AutoRouteInterface $srcAutoRoute, AutoRouteInterface $destAutoRoute)
    {
    }

    /**
     * Remove the given auto route
     *
     * @param AutoRouteInterface $autoRoute
     */
    public function removeAutoRoute(AutoRouteInterface $autoRoute)
    {
    }

    /**
     * Return auto routes which refer to the given content
     * object.
     *
     * @param object|RouteReferrersInterface $content
     *
     * @return array
     */
    public function getReferringAutoRoutes($content)
    {
        return $content->getRoutes();
    }

    /**
     * Create a new redirect route at the path of the given
     * referringAutoRoute.
     *
     * The referring auto route should either be deleted or scheduled to be removed,
     * so the route created here will replace it.
     *
     * The new redirecvt route should redirect the request to the URL determined by
     * the $newRoute.
     *
     * @param AutoRouteInterface $referringAutoRoute
     * @param AutoRouteInterface $newRoute
     */
    public function createRedirectRoute(AutoRouteInterface $referringAutoRoute, AutoRouteInterface $newRoute)
    {
    }

} 
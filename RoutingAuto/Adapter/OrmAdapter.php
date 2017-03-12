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
use Symfony\Cmf\Bundle\RoutingBundle\Model\Route;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
use Positibe\Bundle\CmfRoutingExtraBundle\RoutingAuto\AdapterInterface;
use Positibe\Bundle\CmfRoutingExtraBundle\RoutingAuto\Model\AutoRouteInterface;
use Positibe\Bundle\CmfRoutingExtraBundle\RoutingAuto\UriContext;
use Symfony\Component\Routing\RouterInterface;


/**
 * Class OrmAdapter
 * @package Positibe\Bundle\CmfRoutingExtraBundle\RoutingAuto\Adapter
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class OrmAdapter implements AdapterInterface
{
    private $em;
    private $router;
    private $routeFactory;

    public function __construct(EntityManager $entityManager, RouterInterface $router, RouteFactory $routeFactory)
    {
        $this->em = $entityManager;
        $this->router = $router;
        $this->routeFactory = $routeFactory;
    }

    /**
     * Return locales for object
     *
     * @param $object
     * @return array|void
     */
    public function getLocales($object)
    {
        // TODO: Implement getLocales() method.
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
        // TODO: Implement translateObject() method.
    }

    /**
     * Create a new auto route at the given path
     * with the given document as the content.
     *
     * @param string $path
     * @param object $document
     * @param string $tag
     *
     * @return AutoRouteInterface new route document
     */
    public function createAutoRoute($path, $document, $tag)
    {
        // TODO: Implement createAutoRoute() method.
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
        // TODO: Implement getRealClassName() method.
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
        // TODO: Implement compareAutoRouteContent() method.
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
     *
     * @return null|Route
     */
    public function findRouteForUri($uri)
    {
        return $this->em->getRepository('CmfRoutingBundle:Route')->findOneBy(['staticPrefix' => $uri]);
    }

    public function createRoute($uri, RouteReferrersInterface $entity, $locale, $controller = null)
    {
        $route = $this->routeFactory->createContentRoute(
            $uri,
            $entity,
            $controller,
            $locale
        );
        $entity->addRoute($route);

        return $route;
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
        // TODO: Implement generateAutoRouteTag() method.
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
        // TODO: Implement migrateAutoRouteChildren() method.
    }

    /**
     * Remove the given auto route
     *
     * @param AutoRouteInterface $autoRoute
     */
    public function removeAutoRoute(AutoRouteInterface $autoRoute)
    {
        // TODO: Implement removeAutoRoute() method.
    }

    /**
     * Return auto routes which refer to the given content
     * object.
     *
     * @param object $contentDocument
     *
     * @return array
     */
    public function getReferringAutoRoutes($contentDocument)
    {
        // TODO: Implement getReferringAutoRoutes() method.
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
        // TODO: Implement createRedirectRoute() method.
    }

} 
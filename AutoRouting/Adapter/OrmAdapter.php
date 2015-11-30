<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmRoutingBundle\AutoRouting\Adapter;

use Doctrine\ORM\EntityManager;
use Positibe\Bundle\OrmRoutingBundle\Builder\RouteBuilder;
use Positibe\Bundle\OrmRoutingBundle\Entity\ContentLoader;
use Positibe\Bundle\OrmRoutingBundle\Entity\Route;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
use Symfony\Cmf\Component\RoutingAuto\AdapterInterface;
use Symfony\Cmf\Component\RoutingAuto\Model\AutoRouteInterface;
use Symfony\Cmf\Component\RoutingAuto\UriContext;
use Symfony\Component\Routing\RouterInterface;


/**
 * Class OrmAdapter
 * @package Positibe\Bundle\OrmRoutingBundle\AutoRouting\Adapter
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class OrmAdapter implements AdapterInterface
{
    private $em;
    private $router;
    private $routeBuilder;

    public function __construct(EntityManager $entityManager, RouterInterface $router, RouteBuilder $routeBuilder)
    {
        $this->em = $entityManager;
        $this->router = $router;
        $this->routeBuilder = $routeBuilder;
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
        $route = $this->em->getRepository('PositibeOrmRoutingBundle:Route')->findOneBy(
            array(
                'staticPrefix' => $uri
            )
        );

        ContentLoader::loadContent($route, $this->em, false);

        return $route;
    }

    public function createRoute($uri, RouteReferrersInterface $entity, $locale, $controller = null)
    {
        $route = $this->routeBuilder->createContentRoute(
            $uri,
            $entity,
            $controller
        );
        $route->setLocale($locale);
        $route->setContent($entity);
        $entity->addRoute($route);

        return $route;
    }

    /**
     * Generate a tag which can be used to identify this route from
     * other routes as required.
     *
     * @param UriContext $uriContext
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
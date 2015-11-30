<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmRoutingBundle\Form\DataTransformer;

use Positibe\Bundle\OrmRoutingBundle\Builder\RouteBuilder;
use Positibe\Bundle\OrmRoutingBundle\Entity\Route;
use Positibe\Bundle\OrmRoutingBundle\Model\CustomRouteInformation;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
use Symfony\Component\Form\DataTransformerInterface;


/**
 * Class RoutesToRouteLocaleTransformer
 * @package Positibe\Bundle\OrmRoutingBundle\Form\DataTransformer
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class RoutesToRouteLocaleTransformer implements DataTransformerInterface
{
    private $contentHasRoute;
    private $defaultLocale;
    private $currentLocale;
    private $routeBuider;

    public function __construct(
        RouteReferrersInterface $contentHasRoute,
        RouteBuilder $routeBuilder,
        $defaultLocale,
        $currentLocale = null
    ) {
        $this->contentHasRoute = $contentHasRoute;
        if (!$currentLocale) {
            $this->currentLocale = $defaultLocale;
        } else {
            $this->currentLocale = $currentLocale;
        }
        $this->defaultLocale = $defaultLocale;
        $this->routeBuilder = $routeBuilder;
    }

    /**
     * @param mixed $routes
     * @return mixed|void
     */
    public function transform($routes)
    {
        /** @var Route $route */
        foreach ($routes as $route) {
            if ($route->getLocale() === $this->currentLocale) {
                return $route;
            }
        }

        $route = new Route();

        return $route;
    }

    /**
     * @param mixed $route
     * @return mixed|void
     */
    public function reverseTransform($route)
    {
        $staticPrefix = $route->getStaticPrefix();
        if (!$route->getId() && !empty($staticPrefix)) {
            $this->contentHasRoute->addRoute($route);
            $route->setLocale($this->contentHasRoute->getLocale());
        }

        return $this->contentHasRoute->getRoutes();
    }

} 
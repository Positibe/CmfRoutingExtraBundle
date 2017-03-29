<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\CmfRoutingExtraBundle\Form\DataTransformer;

use Positibe\Bundle\CmfRoutingExtraBundle\Entity\AutoRoute;
use Positibe\Bundle\CmfRoutingExtraBundle\Factory\RouteFactory;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
use Symfony\Component\Form\DataTransformerInterface;


/**
 * Class RoutesToRouteLocaleTransformer
 * @package Positibe\Bundle\CmfRoutingExtraBundle\Form\DataTransformer
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class RoutesToRouteLocaleTransformer implements DataTransformerInterface
{
    private $contentHasRoute;
    private $defaultLocale;
    private $currentLocale;
    private $routeFactory;

    public function __construct(
        RouteReferrersInterface $contentHasRoute,
        RouteFactory $routeFactory,
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
        $this->routeFactory = $routeFactory;
    }

    /**
     * @param mixed $routes
     * @return mixed|void
     */
    public function transform($routes)
    {
        /** @var AutoRoute $route */
        foreach ($routes as $route) {
            if ($route->getLocale() === $this->currentLocale) {
                return $route;
            }
        }

        $route = new AutoRoute();

        return $route;
    }

    /**
     * @param AutoRoute $route
     * @return AutoRoute[]|void
     */
    public function reverseTransform($route)
    {
        $staticPrefix = $route->getStaticPrefix();
        if (!$route->getId() && !empty($staticPrefix)) {
            $this->contentHasRoute->addRoute($route);
            $route->setLocale($this->getLocale());
        }

        return $this->contentHasRoute->getRoutes();
    }

    /**
     * @return null
     */
    public function getLocale()
    {
        if ($this->contentHasRoute instanceof TranslatableInterface && $this->contentHasRoute->getLocale() === null) {
            return $this->currentLocale;
        }

        return $this->contentHasRoute->getLocale();
    }

} 
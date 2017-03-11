<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmRoutingBundle\Factory;

use Positibe\Bundle\OrmRoutingBundle\Entity\Route;
use Positibe\Bundle\OrmRoutingBundle\Model\CustomRouteInformation;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;


/**
 * Class RouteFactory
 * @package Positibe\Bundle\OrmRoutingBundle\Factory
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class RouteFactory
{
    private $controllers = [];

    public function addController($name, $controller)
    {
        $this->controllers[$name] = $controller;
    }

    public function getController()
    {
        return $this->controllers;
    }

    /**
     * @param $path
     * @param RouteReferrersInterface $content
     * @param null $controller
     * @param null $locale
     * @return Route
     */
    public function createContentRoute($path, RouteReferrersInterface $content, $controller = null, $locale = null)
    {
        $route = new Route();
        $route->path = $path;
        $route->setStaticPrefix($path);
        if ($controller !== null) {
            $route->setDefault('_controller', $controller);
        } elseif ($content instanceof CustomRouteInformation) {
            $this->setCustomController($route, $content);
        }
        if ($locale) {
            $route->setLocale($locale);
        }

        $route->setContent($content);

        return $route;
    }

    public function setCustomController(Route $route, CustomRouteInformation $content)
    {
        if ($content->getCustomController() && $controller = $this->controllers[$content->getCustomController()]) {
            $defaults = array('_controller' => $controller[0]);
            foreach ($route->getRequirements() as $key => $default) {
                $defaults[$key] = $route->getDefault($key);
            }

            foreach ($controller[1] as $parameter => $value) {
                $defaults[$parameter] = $value;
            }

            $route->setDefaults($defaults);
        }

        return $route;
    }
} 
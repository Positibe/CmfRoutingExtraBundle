<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmRoutingBundle\Builder;

use Positibe\Bundle\OrmRoutingBundle\Entity\Route;
use Positibe\Bundle\OrmRoutingBundle\Model\CustomRouteInformation;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;

/**
 * Class RouteBuilder
 * @package Positibe\Bundle\OrmRoutingBundle\Builder
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class RouteBuilder
{
    private $controllers;

    public function __construct()
    {
        $this->controllers = array();
    }



    public static function setCustomRouteInformation(Route $route, CustomRouteInformation $content)
    {
        if (!empty($content->getCustomController())) {
            $route->setDefault('_controller', $content->getCustomController());
        }
        if (!empty($content->getCustomTemplate())) {
            $route->setDefault('_template', $content->getCustomTemplate());
        }

        return $route;
    }

    public function addController($name, $controller)
    {
        $this->controllers[$name] = $controller;
    }

    public function getController()
    {
        return $this->controllers;
    }

    public function createContentRoute($path, RouteReferrersInterface $content, $controller = null)
    {
        $route = new Route();
        $route->path = $path;
        $route->setStaticPrefix($path);
        if ($controller !== null) {
            $route->setDefault('_controller', $controller);
        } elseif ($content instanceof CustomRouteInformation) {
            $this->setCustomController($route, $content);
        }

        $route->setContent($content);

        return $route;
    }

    public function setCustomController(Route $route, CustomRouteInformation $content)
    {
        if (!empty($content->getCustomController()) && $controller = $this->controllers[$content->getCustomController()]
        ) {
            $route->setDefault('_controller', $controller[0]);
            foreach ($controller[1] as $parameter => $value) {
                $route->setDefault($parameter, $value);
            }
        }

        return $route;
    }
} 
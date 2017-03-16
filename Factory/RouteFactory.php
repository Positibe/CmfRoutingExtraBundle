<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\CmfRoutingExtraBundle\Factory;

use Positibe\Bundle\CmfRoutingExtraBundle\Model\CustomRouteInformation;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm\ContentRepository;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm\Route;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;


/**
 * Class RouteFactory
 * @package Positibe\Bundle\CmfRoutingExtraBundle\Factory
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class RouteFactory
{
    /** @var  ContentRepository */
    protected $contentRepository;

    public function __construct(ContentRepository $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

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
    public function createContentRoute(
        $path,
        RouteReferrersInterface $content,
        $controller = null,
        $locale = null,
        $defaults = []
    ) {
        $route = new Route();
        $route->setStaticPrefix($path);
        $route->setName(str_replace('/', '-', substr($path, 1)));
        if ($controller !== null) {
            $route->setDefault('_controller', $controller);
        } elseif ($content instanceof CustomRouteInformation) {
            $this->setCustomController($route, $content);
        }
        if ($locale) {
            $route->addDefaults(array('_locale' => $locale));
            $route->addRequirements(array('_locale' => $locale));
        }

        foreach ($defaults as $key => $value) {
            $route->setDefault($key, $value);
        }

        $route->setContent($content);
        if (is_object($content) && $content->getId()) {
            $route->setDefault(RouteObjectInterface::CONTENT_ID, $this->contentRepository->getContentId($content));
        }

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
        } else {
            foreach ($this->controllers as $controllerConfiguration) {
                if ($route->getDefault('_controller') === $controllerConfiguration[0]) {
                    $defaults = $route->getDefaults();
                    unset($defaults['_controller']);
                    foreach ($controllerConfiguration[1] as $parameter => $value) {
                        unset($defaults[$parameter]);
                    }
                    $route->setDefaults($defaults);
                }
            }

        }

        return $route;
    }
} 
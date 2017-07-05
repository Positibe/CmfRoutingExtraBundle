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

use Gedmo\Sluggable\Util\Urlizer;
use Positibe\Bundle\CmfRoutingExtraBundle\Entity\AutoRoute;
use Positibe\Bundle\CmfRoutingExtraBundle\Model\CustomRouteInformationInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm\ContentRepository;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;


/**
 * Class AutoRouteFactory
 * @package Positibe\Bundle\CmfRoutingExtraBundle\Factory
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class RouteFactory
{
    /** @var  ContentRepository */
    protected $contentRepository;

    private $controllers = [];
    private $templates = [];

    public function __construct(ContentRepository $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

    /**
     * @param $path
     * @param RouteReferrersInterface $content
     * @param null $controller
     * @param null $locale
     * @param array $defaults
     * @return AutoRoute
     */
    public function createContentAutoRoute(
        $path,
        RouteReferrersInterface $content,
        $controller = null,
        $locale = null,
        $defaults = []
    ) {
        $route = new AutoRoute();
        $route->setStaticPrefix($path);
        $route->setName(
            method_exists($content, '__toString') ?
                Urlizer::urlize($content->__toString()) :
                str_replace('/', '-', substr($path, 1))
        );
        if ($controller !== null) {
            $route->setDefault('_controller', $controller);
        } elseif ($content instanceof CustomRouteInformationInterface) {
            $this->setCustomController($route, $content);
            $this->setCustomTemplate($route, $content);
            $route->setHost($content->getHost());
        }
        if ($locale) {
            $route->setLocale($locale);
        }

        foreach ($defaults as $key => $value) {
            $route->setDefault($key, $value);
        }

        $route->setContent($content);
        if (is_object($content) && $content->getId()) {
            $route->setDefault(RouteObjectInterface::CONTENT_ID, $this->contentRepository->getContentId($content));
            $route->setRequirement(RouteObjectInterface::CONTENT_ID, $this->contentRepository->getContentId($content));
        }

        return $route;
    }

    /**
     * @param AutoRoute $route
     * @param CustomRouteInformationInterface $content
     * @return AutoRoute
     */
    public function setCustomController(AutoRoute $route, CustomRouteInformationInterface $content)
    {
        if ($content->getCustomController() && $controller = $this->controllers[$content->getCustomController()]) {
            $this->setCustomDefault($route, array_merge(['_controller' => $controller[0]], $controller[1]));
        } else {
            //remove old controller in the route because its content has no controller linked
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

    /**
     * @param AutoRoute $route
     * @param CustomRouteInformationInterface $content
     * @return AutoRoute
     */
    public function setCustomTemplate(AutoRoute $route, CustomRouteInformationInterface $content)
    {
        if ($content->getCustomTemplate() && $template = $this->templates[$content->getCustomTemplate()]) {
            $this->setCustomDefault($route, ['template' => $template], $route->getDefaults());
        } else {
            //remove old controller in the route because its content has no controller linked
            if (!$route->hasDefault('_controller')) {
                $defaults = $route->getDefaults();
                unset($defaults['template']);
                $route->setDefaults($defaults);
            } else {
                foreach ($this->controllers as $controllerConfiguration) {
                    if ($route->getDefault('_controller') === $controllerConfiguration[0]) {
                        if (isset($controllerConfiguration[1]['template'])) {
                            $route->setDefault('template', $controllerConfiguration[1]['template']);
                        }
                    }
                }
            }
        }

        return $route;
    }

    /**
     * @param RouteReferrersInterface|CustomRouteInformationInterface $entity
     * @param AutoRoute $route
     * @return AutoRoute
     */
    public function updateRoutes(RouteReferrersInterface $entity, AutoRoute $route)
    {
        $this->setCustomController($route, $entity);
        $this->setCustomTemplate($route, $entity);
        $route->setHost($entity->getHost());

        return $route;
    }

    public function setCustomDefault(AutoRoute $route, $parameters, $defaults = [])
    {
        foreach ($route->getRequirements() as $key => $default) {
            $defaults[$key] = $route->getDefault($key);
        }

        foreach ($parameters as $parameter => $value) {
            $defaults[$parameter] = $value;
        }

        $route->setDefaults($defaults);

        return $route;
    }

    /**
     * @param $name
     * @param $controller
     */
    public function addController($name, $controller)
    {
        $this->controllers[$name] = $controller;
    }

    /**
     * @param $name
     * @param $template
     */
    public function addTemplate($name, $template)
    {
        $this->templates[$name] = $template;
    }
} 
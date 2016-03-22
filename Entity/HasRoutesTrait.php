<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmRoutingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;

/**
 * Class HasRoutesTrait
 * @package Positibe\Bundle\OrmRoutingBundle\Entity
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
trait HasRoutesTrait
{
    /**
     * @var ArrayCollection|RouteObjectInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Positibe\Bundle\OrmRoutingBundle\Entity\Route", orphanRemoval=TRUE, cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    protected $routes;

    /**
     * @return ArrayCollection|Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param ArrayCollection|\Symfony\Cmf\Component\Routing\RouteObjectInterface[]|Route[] $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }

    /**
     * Add a route to the collection.
     *
     * @param Route $route
     * @return $this
     */
    public function addRoute($route)
    {
        $this->routes->add($route);
        $route->setContent($this);

        return $this;
    }

    /**
     * Remove a route from the collection.
     *
     * @param Route $route
     */
    public function removeRoute($route)
    {
        $this->routes->removeElement($route);
    }

} 
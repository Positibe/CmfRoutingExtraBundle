<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmRoutingBundle\Routing\Enhancer;

use Doctrine\ORM\EntityManager;
use Positibe\Bundle\OrmRoutingBundle\Entity\ContentLoader;
use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RouteContentEnhancer
 * @package Positibe\Bundle\OrmRoutingBundle\Routing\Enhancer
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class RouteContentEnhancer implements RouteEnhancerInterface
{
    /**
     * @var string field to write hashmap lookup result into
     */
    protected $em;

    protected $routeField;

    /**
     * @param EntityManager $entityManager
     * @param string $routeField
     */
    public function __construct(EntityManager $entityManager, $routeField = '_route_object')
    {
        $this->em = $entityManager;
        $this->routeField = $routeField;
    }

    /**
     * Update the defaults based on its own data and the request.
     *
     * @param array $defaults the getRouteDefaults array.
     * @param Request $request the Request instance.
     *
     * @return array the modified defaults. Each enhancer MUST return the
     *               $defaults but may add or remove values.
     */
    public function enhance(array $defaults, Request $request)
    {
        $route = $defaults[$this->routeField];

        ContentLoader::loadContent($route, $this->em);

        return $defaults;
    }


} 
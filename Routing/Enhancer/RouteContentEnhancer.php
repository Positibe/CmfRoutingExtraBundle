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

    protected $routeField = '_route_object';

    protected $contentField = '_content';

    protected $localeField = '_locale';

    protected $requestLocale = '_locale';

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
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
        $defaults[$this->localeField] = $request->get($this->requestLocale, $defaults[$this->localeField]);

        ContentLoader::loadContent($defaults[$this->routeField], $this->em, $defaults[$this->localeField]);

        $defaults[$this->contentField] = $defaults[$this->routeField]->getContent();

        return $defaults;
    }


} 
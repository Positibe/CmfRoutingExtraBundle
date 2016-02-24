<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmRoutingBundle\EventListener;

use Doctrine\ORM\Event\PreFlushEventArgs;
use Positibe\Bundle\OrmRoutingBundle\Model\CustomRouteInformation;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
use Symfony\Cmf\Component\RoutingAuto\Mapping\Exception\ClassNotMappedException;
use Symfony\Cmf\Component\RoutingAuto\UriContextCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class AutoRoutingEntityListener
 * @package Positibe\Bundle\OrmRoutingBundle\EventListener
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class AutoRoutingEntityListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function preFlush(RouteReferrersInterface $entity, PreFlushEventArgs $event)
    {
        $arm = $this->container->get('positibe_orm_routing_auto.auto_route_manager');

        if ($this->isAutoRouteable($entity) && $arm->needNewRoute($entity)) {
            $arm->buildUriContextCollection(new UriContextCollection($entity));
        }

        if ($entity instanceof RouteReferrersInterface &&
            $entity instanceof CustomRouteInformation &&
            $entity->getCustomController()
        ) {
            $routeBuilder = $this->container->get('positibe_orm_routing.route_factory');
            $em = $event->getEntityManager();
            $classMetadata = $em->getClassMetadata('PositibeOrmRoutingBundle:Route');;
            $uow = $em->getUnitOfWork();
            foreach ($entity->getRoutes()->toArray() as $route) {
                $routeBuilder->setCustomController($route, $entity);
                $em->persist($route);
                $uow->computeChangeSet($classMetadata, $route);;
            }
        }
    }

    /**
     * @param $entity
     * @return bool
     */
    private function isAutoRouteable($entity)
    {
        try {
            return (boolean)$this->container->get('positibe_orm_routing_auto.metadata.factory')->getMetadataForClass(
                get_class($entity)
            );
        } catch (ClassNotMappedException $e) {
            return false;
        }
    }
} 
<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\CmfRoutingExtraBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Positibe\Bundle\CmfRoutingExtraBundle\Model\CustomRouteInformation;
use Positibe\Bundle\CmfRoutingExtraBundle\RoutingAuto\Mapping\Exception\ClassNotMappedException;
use Positibe\Bundle\CmfRoutingExtraBundle\RoutingAuto\UriContextCollection;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class RoutingAutoEntityListener
 * @package Positibe\Bundle\CmfRoutingExtraBundle\EventListener
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class RoutingAutoEntityListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function preFlush(RouteReferrersInterface $entity, PreFlushEventArgs $event)
    {
        $arm = $this->container->get('positibe_routing_auto.auto_route_manager');

        if ($this->isAutoRouteable($entity) && $arm->needNewRoute($entity)) {
            $arm->buildUriContextCollection(new UriContextCollection($entity));
        }

        if ($entity instanceof RouteReferrersInterface &&
            $entity instanceof CustomRouteInformation &&
            $entity->getCustomController()
        ) {
            $routeBuilder = $this->container->get('positibe_routing.route_factory');
            $em = $event->getEntityManager();
            $classMetadata = $em->getClassMetadata('CmfRoutingBundle:Route');;
            $uow = $em->getUnitOfWork();
            foreach ($entity->getRoutes() as $route) {
                $routeBuilder->setCustomController($route, $entity);
                $em->persist($route);
                $uow->computeChangeSet($classMetadata, $route);;
            }
        }
    }

    public function postPersist(RouteReferrersInterface $entity, LifecycleEventArgs $event)
    {
        foreach ($entity->getRoutes() as $route) {
            if (!$route->getDefault(RouteObjectInterface::CONTENT_ID)) {
                $route->setDefault(
                    RouteObjectInterface::CONTENT_ID,
                    $this->container->get('cmf_routing.content_repository')->getContentId($entity)
                );
                $event->getEntityManager()->persist($route);
            }
        }
        $event->getEntityManager()->flush();
    }

    /**
     * @param $entity
     * @return bool
     */
    private function isAutoRouteable($entity)
    {
        try {
            return (boolean)$this->container->get('positibe_routing_auto.metadata.factory')
                ->getMetadataForClass(get_class($entity));
        } catch (ClassNotMappedException $e) {
            return false;
        }
    }
}
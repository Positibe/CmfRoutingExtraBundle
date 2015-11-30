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

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Positibe\Bundle\OrmMenuBundle\Menu\Factory\ContentAwareFactory;
use Positibe\Bundle\OrmRoutingBundle\Model\CustomRouteInformation;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
use Symfony\Cmf\Component\RoutingAuto\Mapping\Exception\ClassNotMappedException;
use Symfony\Cmf\Component\RoutingAuto\UriContextCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Positibe\Bundle\OrmRoutingBundle\AutoRouting\AutoRouteManager;


/**
 * Class AutoRoutingListener
 * @package Positibe\Bundle\OrmRoutingBundle\EventListener
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class AutoRoutingListener implements EventSubscriber
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'onFlush' => 'onFlush'
        );
    }


    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $arm = $this->getAutoRouteManager();

        $scheduledInserts = $uow->getScheduledEntityInsertions();
        $scheduledUpdates = $uow->getScheduledEntityUpdates();
        $updates = array_merge($scheduledInserts, $scheduledUpdates);

        foreach ($updates as $entity) {
            if ($config = $this->isAutoRouteable($entity)) {
                if ($arm->needNewRoute($entity)) {
                    $uriContextCollection = new UriContextCollection($entity);
                    $arm->buildUriContextCollection($uriContextCollection);

                    // refactor this.
                    foreach ($uriContextCollection->getUriContexts() as $uriContext) {
                        $autoRoute = $uriContext->getAutoRoute();
                        $em->persist($autoRoute);
                        $uow->computeChangeSets();
                    }
                }
            }
            if ($entity instanceof RouteReferrersInterface &&
                $entity instanceof CustomRouteInformation &&
                !empty($entity->getCustomController())
            ) {
                $routeBuilder = $this->container->get('positibe_orm_routing.route_builder');
                foreach ($entity->getRoutes()->toArray() as $route) {
                    $routeBuilder->setCustomController($route, $entity);
                    $em->persist($route);
                }
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($this->isAutoRouteable($entity) && $entity instanceof RouteReferrersInterface) {
                foreach ($entity->getRoutes() as $route) {
                    $uow->scheduleOrphanRemoval($route);
                }
            }
        }
    }

    /**
     * @return AutoRouteManager
     */
    protected function getAutoRouteManager()
    {
        return $this->container->get('positibe_orm_routing_auto.auto_route_manager');
    }

    private function isAutoRouteable($entity)
    {
        try {
            return (boolean)$this->getMetadataFactory()->getMetadataForClass(get_class($entity));
        } catch (ClassNotMappedException $e) {
            return false;
        }
    }

    protected function getMetadataFactory()
    {
        return $this->container->get('positibe_orm_routing_auto.metadata.factory');
    }
}
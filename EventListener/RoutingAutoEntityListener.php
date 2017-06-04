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
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Positibe\Bundle\CmfRoutingExtraBundle\Entity\AutoRoute;
use Positibe\Bundle\CmfRoutingExtraBundle\Model\CustomRouteInformationInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
use Symfony\Cmf\Component\RoutingAuto\Mapping\Exception\ClassNotMappedException;
use Symfony\Cmf\Component\RoutingAuto\UriContextCollection;
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
    private $flush = false;
    private $updateCustomRouting = false;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param RouteReferrersInterface $entity
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(RouteReferrersInterface $entity, PreUpdateEventArgs $event)
    {
        if ($entity instanceof CustomRouteInformationInterface &&
            (
                $event->hasChangedField('customController') ||
                $event->hasChangedField('customTemplate') ||
                $event->hasChangedField('host')
            )
        ) {
            $this->updateCustomRouting = true;
        }
    }

    public function postPersist(RouteReferrersInterface $entity, LifecycleEventArgs $event)
    {
        $arm = $this->container->get('cmf_routing_auto.auto_route_manager');
        $routeBuilder = $this->container->get('positibe_routing.route_factory');
        $contentRepository = $this->container->get('cmf_routing.content_repository');

        foreach ($entity->getRoutes() as $route) {
            if (!$route->getDefault(RouteObjectInterface::CONTENT_ID)) {
                $route->setDefault(RouteObjectInterface::CONTENT_ID, $contentRepository->getContentId($entity));
                $route->setRequirement(RouteObjectInterface::CONTENT_ID, $contentRepository->getContentId($entity));
                $this->flush = true;
            }

            if ($this->updateCustomRouting) {
                $routeBuilder->updateRoutes($entity, $route);
                $event->getEntityManager()->persist($route);
            }
        }

        if ($this->isAutoRouteable($entity) && $this->needNewRoute($entity)) {
            $arm->buildUriContextCollection(new UriContextCollection($entity));
            $this->flush = true;
        }



        if ($this->flush || $this->updateCustomRouting) {
            $event->getEntityManager()->flush();
            $this->flush = false;
            $this->updateCustomRouting = false;
        }
    }

    /**
     * @param RouteReferrersInterface|CustomRouteInformationInterface $entity
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(RouteReferrersInterface $entity, LifecycleEventArgs $event)
    {
        $this->postPersist($entity, $event);
    }

    /**
     * @param $entity
     * @return bool
     */
    private function isAutoRouteable($entity)
    {
        try {
            return (boolean)$this->container->get('cmf_routing_auto.metadata.factory')
                ->getMetadataForClass(get_class($entity));
        } catch (ClassNotMappedException $e) {
            return false;
        }
    }


    /**
     * @param $entity
     * @return bool
     */
    public function needNewRoute(RouteReferrersInterface $entity)
    {
        $defaultLocale = $this->container->getParameter('locale');
        $currentLocale = $this->getLocale($entity, $defaultLocale);
        foreach ($entity->getRoutes() as $route) {
            if (!$routeLocale = $route->getDefault('_locale')) {
                $routeLocale = $defaultLocale;
            }
            if ($routeLocale === $currentLocale) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $entity
     * @param $defaultLocale
     * @return mixed
     */
    protected function getLocale($entity, $defaultLocale)
    {
        if (method_exists($entity, 'getLocale')) {
            return $entity->getLocale() ?: $defaultLocale;
        }

        return $defaultLocale;
    }


}
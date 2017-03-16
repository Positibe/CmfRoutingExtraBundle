<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\CmfRoutingExtraBundle\Model;

use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm\Route;
use Symfony\Cmf\Component\RoutingAuto\Model\AutoRouteInterface;

/**
 * Class AutoRoute
 * @package Positibe\Bundle\CmfRoutingExtraBundle\Model
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class AutoRoute implements AutoRouteInterface
{
    /** @var  Route */
    protected $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * Set a locale related to this auto route.
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
    }

    /**
     * Return the locale.
     *
     * @return string
     */
    public function getLocale()
    {
        $this->route->getDefault('_locale');
    }

    /**
     * Set the auto route mode.
     *
     * Should be one of AutoRouteInterface::TYPE_* constants
     *
     * @param string $mode
     */
    public function setType($mode)
    {
        // TODO: Implement setType() method.
    }

    /**
     * For use in the REDIRECT mode, specifies the routable object
     * that the AutoRoute should redirect to.
     *
     * @param AutoRouteInterface \Symfony\Cmf\Component\RoutingAuto\Model\AutoRoute to redirect to
     */
    public function setRedirectTarget($autoTarget)
    {
        // TODO: Implement setRedirectTarget() method.
    }

    /**
     * Return the redirect target (when the auto route is of type
     * REDIRECT).
     *
     * @return AutoRouteInterface
     */
    public function getRedirectTarget()
    {
        return $this;
    }

    /**
     * Get the content document this route entry stands for. If non-null,
     * the ControllerClassMapper uses it to identify a controller and
     * the content is passed to the controller.
     *
     * If there is no specific content for this url (i.e. its an "application"
     * page), may return null.
     *
     * @return object the document or entity this route entry points to
     */
    public function getContent()
    {
        return $this->route->getContent();
    }

    /**
     * Get the route name.
     *
     * Normal symfony routes do not know their name, the name is only known
     * from the route collection. In the CMF, it is possible to use route
     * documents outside of collections, and thus useful to have routes provide
     * their name.
     *
     * There are no limitations to allowed characters in the name.
     *
     * @return string|null the route name or null to use the default name
     *                     (e.g. from route collection if known)
     */
    public function getRouteKey()
    {
        return $this->route->getRouteKey();
    }

}
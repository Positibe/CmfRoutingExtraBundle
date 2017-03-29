<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\CmfRoutingExtraBundle\Entity;

use Symfony\Cmf\Bundle\RoutingBundle\Model\Route as CmfRoute;
use Symfony\Cmf\Component\RoutingAuto\Model\AutoRouteInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AutoRoute implement AutoRouteInterface, it's based on Route entity of Cmf
 *
 * @package Positibe\Bundle\CmfRoutingExtraBundle\Entity
 *
 * @ORM\Table(name="positibe_route", indexes={@ORM\Index(name="name_idx", columns={"name"})})
 * @ORM\Entity()
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class AutoRoute extends CmfRoute implements AutoRouteInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=TRUE)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="integer")
     */
    protected $position = 0;


    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->setDefault('_locale', $locale);
        $this->setRequirement('_locale', $locale);
    }

    /**
     * Return the locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->getDefault('_locale');
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
        AutoRouteInterface::TYPE_PRIMARY;
    }

    /**
     * For use in the REDIRECT mode, specifies the routable object
     * that the AutoRoute should redirect to.
     *
     * @param AutoRouteInterface|\Symfony\Cmf\Bundle\RoutingAutoBundle\Model\AutoRoute to redirect to
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
        // TODO: Implement getRedirectTarget() method.
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the sort order of this route.
     *
     * @param int $position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the sort order of this route.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }


    public function setController($controller)
    {
        $this->addDefaults(array('_controller' => $controller));
    }

    public function getController()
    {
        return $this->getDefault('_controller');
    }

}
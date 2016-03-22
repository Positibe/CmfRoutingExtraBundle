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

use Positibe\Component\ContentAware\Entity\ContentClassAwareTrait;
use Positibe\Component\ContentAware\Model\ContentAwareInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Model\Route as BaseRoute;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="positibe_route")
 * @ORM\Entity
 *
 * @package Positibe\Bundle\OrmRoutingBundle\Entity
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class Route extends BaseRoute implements ContentAwareInterface
{
    use ContentClassAwareTrait;

    public $path;

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
     * @param string $staticPrefix
     * @return BaseRoute|void
     */
    public function setStaticPrefix($staticPrefix)
    {
        if ($this->name === null) {
            $this->name = str_replace('/', '-', substr($staticPrefix, 1));
        }

        parent::setStaticPrefix($staticPrefix);
    }

    /**
     * @param mixed $content
     * @return $this|void
     */
    public function setContent($content)
    {
        parent::setContent($content);
        $this->setContentClassByContent($content);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->getDefault('_locale');
    }

    public function setController($controller)
    {
        $this->addDefaults(array('_controller' => $controller));
    }

    public function getController()
    {
        return $this->getDefault('_controller');
    }

    /**
     * @param $name
     * @param $locale
     * @param null $staticPrefix
     * @param null $variablePattern
     * @return Route
     */
    public function createLocaleClone($name, $locale, $staticPrefix = null, $variablePattern = null)
    {
        $route = clone $this;
        $route->setLocaleName($name, $locale);
        if ($staticPrefix) {
            $route->setStaticPrefix($staticPrefix);
        }
        if ($variablePattern) {
            $route->setStaticPrefix($variablePattern);
        }

        return $route;
    }

    /**
     * @param $name
     * @param $locale
     */
    public function setLocaleName($name, $locale)
    {
        $this->setName(sprintf('%s.%s', $name, $locale));
        $this->setLocale($locale);
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->addDefaults(array('_locale' => $locale));
        $this->addRequirements(array('_locale' => $locale));
    }
}
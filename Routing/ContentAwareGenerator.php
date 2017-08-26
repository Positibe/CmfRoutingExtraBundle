<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\CmfRoutingExtraBundle\Routing;

use Symfony\Cmf\Component\Routing\ContentAwareGenerator as CmfContentAwareGerenator;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Routing\RouteReferrersReadInterface;

/**
 * Class ContentAwareGenerator
 * @package Positibe\Bundle\CmfRoutingExtraBundle\Routing
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class ContentAwareGenerator extends CmfContentAwareGerenator
{
    protected $nameFilterRegex;

    /**
     * @return mixed
     */
    public function getNameFilterRegex()
    {
        return $this->nameFilterRegex;
    }

    /**
     * @param mixed $nameFilterRegex
     */
    public function setNameFilterRegex($nameFilterRegex)
    {
        $this->nameFilterRegex = $nameFilterRegex;
    }

    /**
     * We additionally support empty name and data in parameters and RouteAware content.
     *
     * {@inheritdoc}
     */
    public function supports($name)
    {
        if (is_string($name)) {
            if (!preg_match($this->nameFilterRegex, $name)) {
                return false;
            } else {
                return true;
            }
        }

        return !$name || $name instanceof RouteObjectInterface || $name instanceof RouteReferrersReadInterface;

    }

}
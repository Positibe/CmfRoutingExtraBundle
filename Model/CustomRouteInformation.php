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


/**
 * Class CustomRouteInformation
 * @package Positibe\Bundle\CmfRoutingExtraBundle\Model
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
interface CustomRouteInformation
{
    /**
     * @return string|null
     */
    public function getCustomController();

    /**
     * @return string|null
     */
    public function getCustomTemplate();
}
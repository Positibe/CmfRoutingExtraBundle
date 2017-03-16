<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\CmfRoutingExtraBundle\RoutingAuto\Mapping;

use Symfony\Cmf\Component\RoutingAuto\Mapping\Loader\YmlFileLoader;


/**
 * Class ParametersLoader
 * @package Positibe\Bundle\CmfRoutingExtraBundle\RoutingAuto\Mapping
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class ParametersLoader extends YmlFileLoader
{
    public function load($routingAuto, $type = null)
    {
        $metadatas = array();
        foreach ($routingAuto as $className => $mappingNode) {
            $metadatas[] = $this->parseMappingNode($className, $mappingNode, null);
        }

        return $metadatas;
    }
} 
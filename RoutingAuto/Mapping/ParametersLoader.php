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

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;


/**
 * Class ParametersLoader
 * @package Positibe\Bundle\CmfRoutingExtraBundle\RoutingAuto\Mapping
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class ParametersLoader implements LoaderInterface
{
    public function load($RoutingAuto, $type = null)
    {
        $metadatas = array();
        foreach ($RoutingAuto as $className => $mappingNode) {
            $metadatas[] = $this->parseMappingNode($className, $mappingNode);
        }

        return $metadatas;
    }

    /**
     * @param $className
     * @param $mappingNode
     * @return ClassMetadata
     */
    protected function parseMappingNode($className, $mappingNode)
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException(sprintf('Configuration found for unknown class "%s".', $className));
        }
        $classMetadata = new ClassMetadata($className);

        $validKeys = array(
            'uri_schema',
            'conflict_resolver',
            'defunct_route_handler',
            'extend',
            'token_providers',
        );

        foreach ($mappingNode as $key => $value) {
            if (!in_array($key, $validKeys)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid configuration key "%s". Valid keys are "%s"',
                        $key,
                        implode(',', $validKeys)
                    )
                );
            }

            switch ($key) {
                case 'uri_schema':
                    $classMetadata->setUriSchema($value);
                    break;
//                case 'conflict_resolver':
//                    $classMetadata->setConflictResolver($this->parseServiceConfig($mappingNode['conflict_resolver'], $className, $path));
//                    break;
//                case 'defunct_route_handler':
//                    $classMetadata->setDefunctRouteHandler($this->parseServiceConfig($mappingNode['defunct_route_handler'], $className, $path));
//                    break;
//                case 'extend':
//                    $classMetadata->setExtendedClass($mappingNode['extend']);
//                    break;
                case 'token_providers':
                    foreach ($mappingNode['token_providers'] as $tokenName => $provider) {
                        $classMetadata->addTokenProvider(
                            $tokenName,
                            $this->parseServiceConfig($provider, $className)
                        );
                    }
            }
        }

        return $classMetadata;
    }

    /**
     * @param mixed $service
     * @param string $className
     *
     * @return array
     */
    protected function parseServiceConfig($service, $className)
    {
        $name = '';
        $options = array();

        if (is_string($service)) {
            // provider: method
            $name = $service;
        } elseif (1 === count($service) && isset($service[0])) {
            // provider: [method]
            $name = $service[0];
        } elseif (isset($service['name'])) {
            if (isset($service['options'])) {
                // provider: { name: method, options: { slugify: true } }
                $options = $service['options'];
            }

            // provider: { name: method }
            $name = $service['name'];
        } elseif (2 === count($service) && isset($service[0]) && isset($service[1])) {
            // provider: [method, { slugify: true }]
            $name = $service[0];
            $options = $service[1];
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    'Unknown builder service configuration for "%s" for class "%s": %s',
                    $name,
                    $className,
                    json_encode($service)
                )
            );
        }

        return array('name' => $name, 'options' => $options);
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_array($resource);
    }

    /**
     * Gets the loader resolver.
     *
     * @return LoaderResolverInterface A LoaderResolverInterface instance
     */
    public function getResolver()
    {
        // TODO: Implement getResolver() method.
    }

    /**
     * Sets the loader resolver.
     *
     * @param LoaderResolverInterface $resolver A LoaderResolverInterface instance
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        // TODO: Implement setResolver() method.
    }


} 
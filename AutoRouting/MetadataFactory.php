<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmRoutingBundle\AutoRouting;

use Metadata\ClassHierarchyMetadata;
use Metadata\MergeableClassMetadata;
use Metadata\MetadataFactoryInterface;
use Symfony\Cmf\Component\RoutingAuto\Mapping\Exception\CircularReferenceException;
use Symfony\Cmf\Component\RoutingAuto\Mapping\Exception\ClassNotMappedException;


/**
 * Class MetadataFactory
 * @package Positibe\Bundle\OrmRoutingBundle\AutoRouting
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class MetadataFactory implements MetadataFactoryInterface {

    private $resolvedMetadatas;
    private $metadatas;

    /**
     * @param string $className
     * @return ClassHierarchyMetadata|MergeableClassMetadata|null|void
     */
    public function getMetadataForClass($className)
    {
        if (!isset($this->resolvedMetadatas[$className])) {
            $this->resolveMetadata($className);
        }

        return $this->resolvedMetadatas[$className];
    }

    /**
     * @param $class
     */
    protected function resolveMetadata($class)
    {
        $classFqns = array_reverse(class_parents($class));
        $classFqns[] = $class;
        $metadatas = array();
        $addedClasses = array();

        try {
            foreach ($classFqns as $classFqn) {
                foreach ($this->doResolve($classFqn, $addedClasses) as $metadata) {
                    $metadatas[] = $metadata;
                }
            }
        } catch (CircularReferenceException $e) {
            throw new CircularReferenceException(sprintf($e->getMessage(), $class), $e->getCode(), $e->getPrevious());
        }

        if (0 === count($metadatas)) {
            throw new ClassNotMappedException($class);
        }

        $metadata = null;
        foreach ($metadatas as $data) {
            if (null === $metadata) {
                $metadata = $data;
            } else {
                $metadata->merge($data);
            }
        }

        $this->resolvedMetadatas[$class] = $metadata;
    }

    protected function doResolve($classFqn, array &$addedClasses)
    {
        $metadatas = array();

        if (in_array($classFqn, $addedClasses)) {
            throw new CircularReferenceException('Circular reference detected for "%s", make sure you don\'t mix PHP extends and mapping extends.');
        }

        if (isset($this->metadatas[$classFqn])) {
            $currentMetadata = $this->metadatas[$classFqn];
            $addedClasses[] = $classFqn;
            $extendedClass = $currentMetadata->getExtendedClass();

            if (isset($this->metadatas[$extendedClass])) {
                foreach ($this->doResolve($extendedClass, $addedClasses) as $extendData) {
                    $metadatas[] = $extendData;
                }
            }
            $metadatas[] = $this->metadatas[$classFqn];
        }

        return $metadatas;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->metadatas);
    }

} 
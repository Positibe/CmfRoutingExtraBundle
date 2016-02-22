<?php

namespace Positibe\Bundle\OrmRoutingBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Positibe\Bundle\OrmRoutingBundle\DependencyInjection\Compiler\OrmRoutingCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class PositibeOrmRoutingBundle
 * @package Positibe\Bundle\OrmRoutingBundle
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class PositibeOrmRoutingBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        if (!class_exists('Doctrine\ORM\Version')
            || !class_exists('Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass')
            || !class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')
        ) {
            return;
        }
        $container->addCompilerPass(new OrmRoutingCompilerPass());

        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createAnnotationMappingDriver(
                array(
                    'Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm',
                ),
                array(
                    realpath(__DIR__ . '/Entity'),
                ),
                array('cmf_routing.dynamic.persistence.orm.manager_name'),
                'cmf_routing.backend_type_orm',
                array('CmfRoutingBundle' => 'Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm')
            )
        );

    }

}

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

/**
 * Class HasRoutesRepositoryTrait
 * @package Positibe\Bundle\OrmRoutingBundle\Entity
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
trait HasRoutesRepositoryTrait {

    /**
     * @param $route
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByRoute($route)
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.routes', 'r')
            ->where('r = :route')
            ->setParameter('route', $route);

        return $qb->getQuery()->getOneOrNullResult();
    }
} 
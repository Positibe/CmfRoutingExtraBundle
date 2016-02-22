<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\OrmRoutingBundle\EventListener;

use Stof\DoctrineExtensionsBundle\EventListener\LocaleListener as StofLocaleListener;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class LocaleListener
 * @package Positibe\Bundle\OrmRoutingBundle\EventListener
 *
 * @deprecated Rmoved in the next version
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class LocaleListener extends StofLocaleListener
{
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => array(array('onKernelRequest', 15)));
    }

} 
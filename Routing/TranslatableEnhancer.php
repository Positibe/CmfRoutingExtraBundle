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

use Gedmo\Translatable\TranslatableListener;
use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TranslatableEnhance
 * @package Positibe\Bundle\CmfRoutingExtraBundle\Routing
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class TranslatableEnhancer implements RouteEnhancerInterface
{
    private $translatableListener;

    public function __construct(TranslatableListener $translatableListener)
    {
        $this->translatableListener = $translatableListener;
    }

    /**
     * @param array $defaults
     * @param Request $request
     * @return array
     */
    public function enhance(array $defaults, Request $request)
    {
        if (isset($defaults['_locale'])) {
            $this->translatableListener->setTranslatableLocale($defaults['_locale']);
        }

        return $defaults;
    }

}
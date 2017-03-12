<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Positibe\Bundle\CmfRoutingExtraBundle\RoutingAuto;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface TokenProviderInterface
{
    /**
     * Return a token value for the given configuration and
     * document.
     *
     * @param UriContext $uriContext
     * @param array  $options
     *
     * @return string
     */
    public function provideValue(UriContext $uriContext, $options);

    /**
     * Configure the options for this token provider.
     *
     * @param OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver);
}

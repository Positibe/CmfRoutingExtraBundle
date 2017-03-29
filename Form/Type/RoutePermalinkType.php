<?php
/**
 * This file is part of the PositibeLabs Projects.
 *
 * (c) Pedro Carlos Abreu <pcabreus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Positibe\Bundle\CmfRoutingExtraBundle\Form\Type;

use Positibe\Bundle\CmfRoutingExtraBundle\Factory\RouteFactory;
use Positibe\Bundle\CmfRoutingExtraBundle\Form\DataTransformer\RoutesToRouteLocaleTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RoutePermalinkType
 * @package Positibe\Bundle\CmfRoutingExtraBundle\Form\Type
 *
 * @author Pedro Carlos Abreu <pcabreus@gmail.com>
 */
class RoutePermalinkType extends AbstractType
{
    private $defaultLocale;
    private $routeFactory;

    public function __construct(RouteFactory $routeFactory, $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
        $this->routeFactory = $routeFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new RoutesToRouteLocaleTransformer(
            $options['content_has_routes'],
            $this->routeFactory,
            $this->defaultLocale,
            $options['current_locale']
        );

        $builder->addModelTransformer($transformer);
        $builder->add(
            'static_prefix',
            'text',
            array(
                'label' => 'route.form.permalink',
                'translation_domain' => 'PositibeCmfRoutingExtraBundle',
                'required' => false,
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Positibe\Bundle\CmfRoutingExtraBundle\Entity\AutoRoute',
                'current_locale' => null,
            ]
        );
        $resolver->setRequired(['content_has_routes', 'current_locale',]);
        $resolver
            ->addAllowedTypes('content_has_routes', 'Symfony\Cmf\Component\Routing\RouteReferrersInterface')
            ->addAllowedTypes('current_locale', ['null', 'string',]);
    }
}
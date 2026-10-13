<?php

/*
 * This file is part of the Sylius ProductBundle Plugin package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\AdminBundle\Form\Type\ProductType;
use Sylius\ProductBundlePlugin\Form\Type\AddProductBundleToCartType;
use Sylius\ProductBundlePlugin\Twig\Component\Product\AddToCartFormComponent;
use Sylius\ProductBundlePlugin\Twig\Component\Product\FormComponent;
use Sylius\ProductBundlePlugin\Twig\Extension\ProductBundleOrderItemExtension;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.product.form', FormComponent::class)
        ->args([
            service('sylius.repository.product'),
            service('form.factory'),
            '%sylius.model.product.class%',
            ProductType::class,
            service('sylius.generator.slug'),
            service('sylius.repository.product_attribute'),
            service('sylius.factory.product'),
        ])
        ->call('setLiveResponder', [
            service('ux.live_component.live_responder'),
        ])
        ->tag('sylius.live_component.admin', [
            'key' => 'sylius_admin:product:form',
        ])
    ;

    $services->set('sylius_product_bundle.twig.extension.smash_promotion', ProductBundleOrderItemExtension::class)
        ->args([
            service('twig'),
        ])
        ->tag('twig.extension')
    ;

    // Bundle-aware replacement for the Sylius component
    $services->set('sylius_product_bundle.shop.twig.component.product.add_to_cart_form', AddToCartFormComponent::class)
        ->args([
            service('form.factory'),
            service('doctrine.orm.entity_manager'),
            service('router'),
            service('request_stack'),
            service('event_dispatcher'),
            service('sylius.context.cart'),
            service('sylius.factory.add_to_cart_command'),
            service('sylius.factory.order_item'),
            \Sylius\Bundle\ShopBundle\Form\Type\AddToCartType::class,
            service('sylius.repository.product'),
            service('sylius.repository.product_variant'),
            service('sylius_product_bundle.factory.add_product_bundle_to_cart_dto'),
            AddProductBundleToCartType::class,
        ])
        ->call('setLiveResponder', [
            service('ux.live_component.live_responder'),
        ])
        ->tag('sylius.live_component.shop', [
            'key' => 'sylius_shop:product:add_to_cart_form',
        ])
    ;

    // Replace Sylius component with our version
    $services->alias('sylius_shop.twig.component.product.add_to_cart_form', 'sylius_product_bundle.shop.twig.component.product.add_to_cart_form');
};

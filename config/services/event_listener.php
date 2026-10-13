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

use Sylius\ProductBundlePlugin\EventListener\AddProductToProductBundleWhenEditNormalProductEventListener;
use Sylius\ProductBundlePlugin\EventListener\CartItemAddListener;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_product_bundle_plugin.request_listener.add_product_to_product_bundle_when_edit_normal_product', AddProductToProductBundleWhenEditNormalProductEventListener::class)
        ->tag('kernel.event_listener', [
            'event' => 'sylius.product.pre_update',
            'method' => 'addProductToProductBundle',
        ])
        ->tag('kernel.event_listener', [
            'event' => 'sylius.product.pre_create',
            'method' => 'addProductToProductBundle',
        ])
    ;

    $services->set('sylius_product_bundle_plugin.listener.cart_item_add', CartItemAddListener::class)
        ->decorate('sylius_shop.listener.cart_item_add')
        ->args([
            service('sylius.modifier.order'),
        ])
        ->tag('kernel.event_listener', [
            'event' => 'sylius.cart_item_add',
            'method' => 'addToOrder',
        ])
    ;
};

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

use Sylius\ProductBundlePlugin\Factory\AddProductBundleItemToCartCommandFactory;
use Sylius\ProductBundlePlugin\Factory\AddProductBundleToCartCommandFactory;
use Sylius\ProductBundlePlugin\Factory\AddProductBundleToCartDtoFactory;
use Sylius\ProductBundlePlugin\Factory\OrderItemFactory;
use Sylius\ProductBundlePlugin\Factory\ProductBundleOrderItemFactory;
use Sylius\ProductBundlePlugin\Factory\ProductFactory;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_product_bundle.custom_factory.product', ProductFactory::class)
        ->decorate('sylius.factory.product')
        ->args([
            service('.inner'),
            service('sylius_product_bundle.factory.product_bundle'),
        ])
    ;

    $services->set('sylius_product_bundle.custom_factory.product_bundle_order_item', ProductBundleOrderItemFactory::class)
        ->decorate('sylius_product_bundle.factory.product_bundle_order_item')
        ->args([
            service('.inner'),
        ])
    ;

    $services->set('sylius_product_bundle.custom_factory.order_item', OrderItemFactory::class)
        ->decorate('sylius.factory.order_item', null, 128)
        ->args([
            service('.inner'),
        ])
    ;

    $services->set('sylius_product_bundle.factory.add_product_bundle_item_to_cart_command', AddProductBundleItemToCartCommandFactory::class);

    $services->set('sylius_product_bundle.factory.add_product_bundle_to_cart_command', AddProductBundleToCartCommandFactory::class);

    $services->set('sylius_product_bundle.factory.add_product_bundle_to_cart_dto', AddProductBundleToCartDtoFactory::class)
        ->args([
            service('sylius_product_bundle.factory.add_product_bundle_item_to_cart_command'),
        ])
    ;
};

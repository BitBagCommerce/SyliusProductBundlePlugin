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

use Sylius\ProductBundlePlugin\Handler\AddProductBundleToCartHandler;
use Sylius\ProductBundlePlugin\Handler\Api\AddProductBundleToCartHandler as ApiAddProductBundleToCartHandler;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_product_bundle.handler.add_product_bundle_to_cart', AddProductBundleToCartHandler::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.product'),
            service('sylius_product_bundle.processor.cart_item_processor'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('sylius_product_bundle.api.handler.add_product_bundle_to_cart', ApiAddProductBundleToCartHandler::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.product'),
            service('sylius_product_bundle.processor.cart_item_processor'),
        ])
        ->tag('messenger.message_handler')
    ;
};

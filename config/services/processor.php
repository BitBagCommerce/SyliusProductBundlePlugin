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

use Sylius\ProductBundlePlugin\Handler\AddProductBundleToCartHandler\CartProcessor;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_product_bundle.processor.cart_item_processor', CartProcessor::class)
        ->args([
            service('sylius.modifier.order_item_quantity'),
            service('sylius_product_bundle.custom_factory.product_bundle_order_item'),
            service('sylius.modifier.order'),
            service('sylius_product_bundle.custom_factory.order_item'),
        ])
    ;
};

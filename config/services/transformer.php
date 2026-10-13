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

use Sylius\ProductBundlePlugin\DataTransformer\AddProductBundleToCartDtoDataTransformer;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_product_bundle.data_transformer.add_product_bundle_to_cart_dto', AddProductBundleToCartDtoDataTransformer::class)
        ->tag('api_platform.data_transformer')
    ;
};

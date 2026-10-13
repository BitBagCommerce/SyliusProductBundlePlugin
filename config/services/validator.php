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

use Sylius\ProductBundlePlugin\Validator\HasAvailableProductBundleValidator;
use Sylius\ProductBundlePlugin\Validator\HasExistingCartValidator;
use Sylius\ProductBundlePlugin\Validator\HasProductBundleValidator;
use Sylius\ProductBundlePlugin\Validator\SequentiallyValidator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_product_bundle.validator.has_available_product_bundle', HasAvailableProductBundleValidator::class)
        ->args([
            service('sylius.repository.product'),
            service('sylius.repository.order'),
            service('sylius.checker.inventory.availability'),
        ])
        ->tag('validator.constraint_validator', [
            'alias' => 'sylius_product_bundle_validator_has_available_product_bundle',
        ])
    ;

    $services->set('sylius_product_bundle.validator.has_existing_cart', HasExistingCartValidator::class)
        ->args([
            service('sylius.repository.order'),
        ])
        ->tag('validator.constraint_validator', [
            'alias' => 'sylius_product_bundle_validator_has_existing_cart',
        ])
    ;

    $services->set('sylius_product_bundle.validator.has_product_bundle', HasProductBundleValidator::class)
        ->args([
            service('sylius.repository.product'),
        ])
        ->tag('validator.constraint_validator', [
            'alias' => 'sylius_product_bundle_validator_has_product_bundle',
        ])
    ;

    $services->set('sylius_product_bundle.validator.sequentially', SequentiallyValidator::class)
        ->tag('validator.constraint_validator', [
            'alias' => 'sylius_product_bundle_validator_sequentially',
        ])
    ;
};

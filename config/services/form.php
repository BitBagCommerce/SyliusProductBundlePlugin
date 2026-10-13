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
use Sylius\ProductBundlePlugin\Form\Extension\ProductTypeExtension;
use Sylius\ProductBundlePlugin\Form\Type\ProductBundleItemType;
use Sylius\ProductBundlePlugin\Form\Type\ProductBundleType;

return static function (ContainerConfigurator $container): void {
    $parameters = $container->parameters();
    $parameters->set('sylius_product_bundle.form.type.product_bundle.validation_groups', ['sylius_product_bundle']);
    $parameters->set('sylius_product_bundle.form.type.product_bundle_item.validation_groups', ['sylius_product_bundle']);

    $services = $container->services();

    $services->set('sylius_product_bundle.form.type.product_bundle', ProductBundleType::class)
        ->args([
            '%sylius_product_bundle.model.product_bundle.class%',
            '%sylius_product_bundle.form.type.product_bundle.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services->set('sylius_product_bundle.form.type.product_bundle_item', ProductBundleItemType::class)
        ->args([
            '%sylius_product_bundle.model.product_bundle_item.class%',
            '%sylius_product_bundle.form.type.product_bundle_item.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services->set('sylius_product_bundle.form.extension.type.product', ProductTypeExtension::class)
        ->tag('form.type_extension', [
            'extended-type' => ProductType::class,
        ])
    ;
};

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

use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Tests\Sylius\ProductBundlePlugin\Behat\Context\Setup\ProductBundleContext;
use Tests\Sylius\ProductBundlePlugin\Behat\Context\Ui\ProductBundleContext as UiProductBundleContext;
use Tests\Sylius\ProductBundlePlugin\Behat\Page\Admin\CreateBundledProductPage;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()->public();

    $services->set('sylius_product_bundle_plugin.behat.context.setup.product_bundle', ProductBundleContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.factory.taxon'),
            service('sylius.repository.product'),
            service('sylius.factory.product_taxon'),
            service('sylius.manager.product_taxon'),
            service('sylius_product_bundle.custom_factory.product'),
            service('sylius_product_bundle.factory.product_bundle_item'),
            service('sylius.factory.channel_pricing'),
            service('sylius.resolver.product_variant.default'),
            service('sylius.generator.slug'),
        ])
        ->tag('behat.context')
    ;

    $services->set('sylius_product_bundle_plugin.behat.page.create_bundled_product_page', CreateBundledProductPage::class)
        ->private()
        ->parent('sylius.behat.page.admin.crud.create')
        ->args([
            'sylius_product_bundle_admin_product_create_bundle',
            service(AutocompleteHelperInterface::class),
        ])
    ;

    $services->set('sylius_product_bundle_plugin.behat.context.ui.product_bundle', UiProductBundleContext::class)
        ->args([
            service('sylius_product_bundle_plugin.behat.page.create_bundled_product_page'),
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.shop.cart_summary'),
            service('behat.mink.default_session'),
        ])
        ->tag('behat.context')
    ;
};

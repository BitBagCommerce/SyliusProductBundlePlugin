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

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('bundled_product'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.zone',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.locale',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.cart',
                'sylius.behat.context.ui.shop.cart',
                'sylius.behat.context.ui.shop.checkout.addressing',
                'sylius.behat.context.ui.shop.checkout',
                'sylius.behat.context.ui.shop.checkout.complete',
                'sylius.behat.context.ui.shop.checkout.thank_you',
                'sylius.behat.context.ui.admin.notification',
                'sylius_product_bundle_plugin.behat.context.setup.product_bundle',
                'sylius_product_bundle_plugin.behat.context.ui.product_bundle',
            )
            ->withFilter(new TagFilter('@bundled_product')),
        ),
    )
;

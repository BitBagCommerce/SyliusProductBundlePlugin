<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Validator;

use Symfony\Component\Validator\Constraint;

final class HasAvailableProductBundle extends Constraint
{
    public const PRODUCT_DISABLED_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.product_disabled';

    public const PRODUCT_VARIANT_DISABLED_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.product_variant_disabled';

    public const PRODUCT_VARIANT_INSUFFICIENT_STOCK_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.product_variant_insufficient_stock';

    public const PRODUCT_DOESNT_EXIST_IN_CHANNEL_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.product_doesnt_exist_in_channel';

    public function validatedBy(): string
    {
        return 'bitbag_sylius_product_bundle_validator_has_available_product_bundle';
    }

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}

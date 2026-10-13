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

namespace Sylius\ProductBundlePlugin\Validator;

use Symfony\Component\Validator\Constraint;

final class HasAvailableProductBundle extends Constraint
{
    public const PRODUCT_DISABLED_MESSAGE = 'sylius_product_bundle.add_to_cart.product_disabled';

    public const PRODUCT_VARIANT_DISABLED_MESSAGE = 'sylius_product_bundle.add_to_cart.product_variant_disabled';

    public const PRODUCT_VARIANT_INSUFFICIENT_STOCK_MESSAGE = 'sylius_product_bundle.add_to_cart.product_variant_insufficient_stock';

    public const PRODUCT_DOESNT_EXIST_IN_CHANNEL_MESSAGE = 'sylius_product_bundle.add_to_cart.product_doesnt_exist_in_channel';

    public function validatedBy(): string
    {
        return 'sylius_product_bundle_validator_has_available_product_bundle';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

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

final class HasExistingCart extends Constraint
{
    public const CART_DOESNT_EXIST_MESSAGE = 'sylius_product_bundle.add_to_cart.cart_doesnt_exist';

    public function validatedBy(): string
    {
        return 'sylius_product_bundle_validator_has_existing_cart';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

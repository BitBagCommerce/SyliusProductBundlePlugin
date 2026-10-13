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

final class HasProductBundle extends Constraint
{
    public const PRODUCT_DOESNT_EXIST_MESSAGE = 'sylius_product_bundle.add_to_cart.product_doesnt_exist';

    public const NOT_A_BUNDLE_MESSAGE = 'sylius_product_bundle.product.not_a_bundle';

    public function validatedBy(): string
    {
        return 'sylius_product_bundle_validator_has_product_bundle';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Validator;

use Symfony\Component\Validator\Constraint;

final class ValidAddProductBundleToCartCommand extends Constraint
{
    public const NO_ORDER_ID_OR_TOKEN_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.no_order_id_or_token';

    public const PRODUCT_BUNDLE_DOESNT_EXIST_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.product_bundle_doesnt_exist';

    public const PRODUCT_DISABLED_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.product_disabled';

    public const PRODUCT_VARIANT_DISABLED_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.product_variant_disabled';

    public const CART_DOESNT_EXIST_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.cart_doesnt_exist';

    public const PRODUCT_VARIANT_INSUFFICIENT_STOCK_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.product_variant_insufficient_stock';

    public const PRODUCT_DOESNT_EXIST_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.product_doesnt_exist';

    public function validatedBy(): string
    {
        return 'bitbag_sylius_product_bundle_validator_valid_add_product_bundle_to_cart_command';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

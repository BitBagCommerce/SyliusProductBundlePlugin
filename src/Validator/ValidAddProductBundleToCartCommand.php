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
    const NO_ORDER_ID_OR_TOKEN_MESSAGE = "Neither order's ID nor order's token provided";
    const PRODUCT_BUNDLE_DOESNT_EXIST_MESSAGE = "Product bundle with id {{ id }} does not exist.";
    const PRODUCT_DISABLED_MESSAGE = "Product with {{ code }} code is not enabled.";
    const PRODUCT_VARIANT_DISABLED_MESSAGE = "Product Variant with {{ code }} code is not enabled.";
    const CART_DOESNT_EXIST_MESSAGE = "Cart with provided ID or token does not exist.";
    const PRODUCT_VARIANT_INSUFFICIENT_STOCK_MESSAGE = "Product variant with {{ code }} code does not have sufficient stock.";
    const PRODUCT_DOESNT_EXIST_MESSAGE = "Product {{ name }} does not exist.";

    public function validatedBy(): string
    {
        return 'bitbag_sylius_product_bundle_validator_valid_add_product_bundle_to_cart_command';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Validator;

use Symfony\Component\Validator\Constraint;

final class HasExistingCart extends Constraint
{
    public const CART_DOESNT_EXIST_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.cart_doesnt_exist';

    public function validatedBy(): string
    {
        return 'bitbag_sylius_product_bundle_validator_has_existing_cart';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

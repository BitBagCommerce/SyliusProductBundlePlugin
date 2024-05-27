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

final class HasProductBundle extends Constraint
{
    public const PRODUCT_DOESNT_EXIST_MESSAGE = 'bitbag_sylius_product_bundle.add_to_cart.product_doesnt_exist';

    public const NOT_A_BUNDLE_MESSAGE = 'bitbag_sylius_product_bundle.product.not_a_bundle';

    public function validatedBy(): string
    {
        return 'bitbag_sylius_product_bundle_validator_has_product_bundle';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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

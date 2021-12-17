<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Validator;

use Symfony\Component\Validator\Constraint;

final class IsProductBundle extends Constraint
{
    public const NOT_A_BUNDLE = 'bitbag_sylius_product_bundle.product.not_a_bundle';

    public function validatedBy(): string
    {
        return 'bitbag_sylius_product_bundle_validator_is_product_bundle';
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}

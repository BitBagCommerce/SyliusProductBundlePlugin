<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject;

use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantMother
{
    public static function create(): ProductVariantInterface
    {
        return new ProductVariant();
    }

    public static function createWithCode(string $code): ProductVariantInterface
    {
        $productVariant = self::create();

        $productVariant->setCode($code);

        return $productVariant;
    }

    public static function createDisabledWithCode(string $code): ProductVariantInterface
    {
        $productVariant = self::createWithCode($code);

        $productVariant->disable();

        return $productVariant;
    }
}

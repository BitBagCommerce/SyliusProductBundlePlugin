<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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

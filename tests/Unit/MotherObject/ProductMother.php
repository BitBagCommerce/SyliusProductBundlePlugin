<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Tests\BitBag\SyliusProductBundlePlugin\Entity\Product;

final class ProductMother
{
    public static function create(): ProductInterface
    {
        return new Product();
    }

    public static function createWithBundle(ProductBundleInterface $productBundle): ProductInterface
    {
        $product = self::create();

        $product->setProductBundle($productBundle);

        return $product;
    }

    public static function createWithProductVariantAndCode(
        ProductVariantInterface $productVariant,
        string $code,
    ): ProductInterface {
        $product = self::create();

        $product->addVariant($productVariant);
        $product->setCode($code);

        return $product;
    }

    public static function createWithChannelAndProductVariantAndCode(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        string $code,
    ): ProductInterface {
        $product = self::createWithProductVariantAndCode($productVariant, $code);

        $product->addChannel($channel);

        return $product;
    }

    public static function createWithCode(string $code): ProductInterface
    {
        $product = self::create();

        $product->setCode($code);

        return $product;
    }

    public static function createDisabledWithCode(string $code): ProductInterface
    {
        $product = self::createWithCode($code);

        $product->disable();

        return $product;
    }
}

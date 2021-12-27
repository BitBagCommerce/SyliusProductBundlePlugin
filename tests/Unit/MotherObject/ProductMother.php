<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
        string $code
    ): ProductInterface {
        $product = self::create();

        $product->addVariant($productVariant);
        $product->setCode($code);

        return $product;
    }

    public static function createWithChannelAndProductVariantAndCode(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        string $code
    ): ProductInterface {
        $product = self::createWithProductVariantAndCode($productVariant, $code);

        $product->addChannel($channel);

        return $product;
    }

    public static function createDisabledWithCode(string $code): ProductInterface
    {
        $product = self::create();

        $product->setCode($code);
        $product->disable();

        return $product;
    }
}

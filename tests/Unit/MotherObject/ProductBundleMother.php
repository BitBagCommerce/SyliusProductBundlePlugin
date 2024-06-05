<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundle;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;

final class ProductBundleMother
{
    public static function create(): ProductBundleInterface
    {
        return new ProductBundle();
    }

    /**
     * @param ProductBundleItemInterface ...$bundleItems
     */
    public static function createWithBundleItems(...$bundleItems): ProductBundleInterface
    {
        $productBundle = self::create();

        foreach ($bundleItems as $bundleItem) {
            $productBundle->addProductBundleItem($bundleItem);
        }

        return $productBundle;
    }
}

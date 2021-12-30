<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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

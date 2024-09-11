<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Factory;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommandInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ProductBundleOrderItemFactoryInterface extends FactoryInterface
{
    public function createFromProductBundleItem(ProductBundleItemInterface $bundleItem): ProductBundleOrderItemInterface;

    public function createFromAddProductBundleItemToCartCommand(
        AddProductBundleItemToCartCommandInterface $addItemToCartCommand,
    ): ProductBundleOrderItemInterface;
}

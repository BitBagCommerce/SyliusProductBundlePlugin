<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Factory;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ProductBundleOrderItemFactoryInterface extends FactoryInterface
{
    public function createFromProductBundleItem(ProductBundleItemInterface $bundleItem): ProductBundleOrderItemInterface;
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Factory;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductBundleOrderItemFactory implements ProductBundleOrderItemFactoryInterface
{
    public function __construct(
        private FactoryInterface $decoratedFactory,
    ) {
    }

    public function createNew(): ProductBundleOrderItemInterface
    {
        /** @var ProductBundleOrderItemInterface $productBundleOrderItem */
        $productBundleOrderItem = $this->decoratedFactory->createNew();

        return $productBundleOrderItem;
    }

    public function createFromProductBundleItem(ProductBundleItemInterface $bundleItem): ProductBundleOrderItemInterface
    {
        /** @var ProductBundleOrderItemInterface $productBundleOrderItem */
        $productBundleOrderItem = $this->decoratedFactory->createNew();

        $productBundleOrderItem->setProductBundleItem($bundleItem);
        $productBundleOrderItem->setProductVariant($bundleItem->getProductVariant());
        $productBundleOrderItem->setQuantity($bundleItem->getQuantity());

        return $productBundleOrderItem;
    }
}

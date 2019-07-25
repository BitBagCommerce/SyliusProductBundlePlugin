<?php

namespace BitBag\SyliusProductBundlePlugin\Command;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class AddProductBundleItemToCartCommand
{
    /** @var ProductBundleItemInterface */
    private $productBundleItem;

    /** @var ProductVariantInterface */
    private $productVariant;

    /** @var int */
    private $quantity;

    public function __construct(ProductBundleItemInterface $productBundleItem)
    {
        $this->productBundleItem = $productBundleItem;
        $this->productVariant = $productBundleItem->getProductVariant();
        $this->quantity = $productBundleItem->getQuantity();
    }

    public function getProductBundleItem(): ProductBundleItemInterface
    {
        return $this->productBundleItem;
    }

    public function getProductVariant(): ProductVariantInterface
    {
        return $this->productVariant;
    }

    public function setProductVariant(ProductVariantInterface $productVariant): void
    {
        $this->productVariant = $productVariant;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}

<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Command;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class AddProductBundleItemToCartCommand
{
    /** @var ProductBundleItemInterface */
    private $productBundleItem;

    /** @var ProductVariantInterface|null */
    private $productVariant;

    /** @var int|null */
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

    public function getProductVariant(): ?ProductVariantInterface
    {
        return $this->productVariant;
    }

    public function setProductVariant(ProductVariantInterface $productVariant): void
    {
        $this->productVariant = $productVariant;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }
}

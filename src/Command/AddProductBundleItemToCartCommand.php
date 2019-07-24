<?php

namespace BitBag\SyliusProductBundlePlugin\Command;

use Sylius\Component\Core\Model\ProductVariantInterface;

final class AddProductBundleItemToCartCommand
{
    /** @var ProductVariantInterface */
    private $productVariant;

    /** @var int */
    private $quantity;

    public function __construct(ProductVariantInterface $productVariant, int $quantity)
    {
        $this->productVariant = $productVariant;
        $this->quantity = $quantity;
    }

    public function getProductVariant(): ProductVariantInterface
    {
        return $this->productVariant;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}

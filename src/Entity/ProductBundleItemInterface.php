<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Entity;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ProductBundleItemInterface extends ResourceInterface, TimestampableInterface
{
    public function getProductVariant(): ?ProductVariantInterface;

    public function setProductVariant(?ProductVariantInterface $productVariant): void;

    public function getQuantity(): ?int;

    public function setQuantity(?int $quantity): void;

    public function getProductBundle(): ?ProductBundleItemInterface;

    public function setProductBundle(?ProductBundleItemInterface $productBundle): void;
}

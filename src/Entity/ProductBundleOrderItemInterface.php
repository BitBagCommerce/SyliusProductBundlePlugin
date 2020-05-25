<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Entity;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ProductBundleOrderItemInterface extends ResourceInterface, TimestampableInterface
{
    public function getOrderItem(): ?OrderItemInterface;

    public function setOrderItem(?OrderItemInterface $orderItem): void;

    public function getProductBundleItem(): ?ProductBundleItemInterface;

    public function setProductBundleItem(?ProductBundleItemInterface $productBundleItem): void;

    public function getProductVariant(): ?ProductVariantInterface;

    public function setProductVariant(?ProductVariantInterface $productVariant): void;

    public function getQuantity(): ?int;

    public function setQuantity(?int $quantity): void;
}

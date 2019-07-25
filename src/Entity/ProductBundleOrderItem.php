<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Entity;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class ProductBundleOrderItem implements ProductBundleOrderItemInterface
{
    use TimestampableTrait;

    /** @var int */
    private $id;

    /** @var OrderItemInterface|null */
    private $orderItem;

    /** @var ProductBundleItemInterface|null */
    private $productBundleItem;

    /** @var ProductVariantInterface|null */
    private $productVariant;

    /** @var int|null */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderItem(): ?OrderItemInterface
    {
        return $this->orderItem;
    }

    public function setOrderItem(?OrderItemInterface $orderItem): void
    {
        $this->orderItem = $orderItem;
    }

    public function getProductBundleItem(): ?ProductBundleItemInterface
    {
        return $this->productBundleItem;
    }

    public function setProductBundleItem(?ProductBundleItemInterface $productBundleItem): void
    {
        $this->productBundleItem = $productBundleItem;
    }

    public function getProductVariant(): ?ProductVariantInterface
    {
        return $this->productVariant;
    }

    public function setProductVariant(?ProductVariantInterface $productVariant): void
    {
        $this->productVariant = $productVariant;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }
}

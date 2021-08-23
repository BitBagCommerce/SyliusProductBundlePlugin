<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Entity;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class ProductBundleOrderItem implements ProductBundleOrderItemInterface
{
    use TimestampableTrait;

    /** @var int */
    protected $id;

    /** @var OrderItemInterface|null */
    protected $orderItem;

    /** @var ProductBundleItemInterface|null */
    protected $productBundleItem;

    /** @var ProductVariantInterface|null */
    protected $productVariant;

    /** @var int|null */
    protected $quantity;

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

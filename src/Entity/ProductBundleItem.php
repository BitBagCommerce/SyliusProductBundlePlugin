<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Entity;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class ProductBundleItem implements ProductBundleItemInterface
{
    use TimestampableTrait;

    /** @var int */
    private $id;

    /** @var ProductVariantInterface */
    private $productVariant;

    /** @var int */
    private $quantity;

    /** @var ProductBundleInterface|null */
    private $productBundle;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProductBundle(): ?ProductBundleInterface
    {
        return $this->productBundle;
    }

    public function setProductBundle(?ProductBundleInterface $productBundle): void
    {
        $this->productBundle = $productBundle;
    }
}

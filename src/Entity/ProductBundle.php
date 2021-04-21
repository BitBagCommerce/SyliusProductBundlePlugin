<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class ProductBundle implements ProductBundleInterface
{
    use TimestampableTrait;

    /** @var int */
    protected $id;

    /** @var ProductInterface|null */
    protected $product;

    /** @var ProductBundleItemInterface[]|Collection */
    protected $productBundleItems;

    /** @var bool */
    protected $isPackedProduct = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->productBundleItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?ProductInterface
    {
        return $this->product;
    }

    public function setProduct(?ProductInterface $product): void
    {
        $this->product = $product;
    }

    public function getProductBundleItems(): Collection
    {
        return $this->productBundleItems;
    }

    public function addProductBundleItem(ProductBundleItemInterface $productBundleItem): void
    {
        if (!$this->hasProductBundleItem($productBundleItem)) {
            $productBundleItem->setProductBundle($this);

            $this->productBundleItems->add($productBundleItem);
        }
    }

    public function removeProductBundleItem(ProductBundleItemInterface $productBundleItem): void
    {
        if ($this->hasProductBundleItem($productBundleItem)) {
            $productBundleItem->setProductBundle(null);

            $this->productBundleItems->removeElement($productBundleItem);
        }
    }

    public function hasProductBundleItem(ProductBundleItemInterface $productBundleItem): bool
    {
        return $this->productBundleItems->contains($productBundleItem);
    }

    public function isPackedProduct(): bool
    {
        return $this->isPackedProduct;
    }

    public function setIsPackedProduct(bool $isPackedProduct): void
    {
        $this->isPackedProduct = $isPackedProduct;
    }
}

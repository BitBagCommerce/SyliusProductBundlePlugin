<?php

/*
 * This file is part of the Sylius ProductBundle Plugin package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ProductBundlePlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class ProductBundle implements ProductBundleInterface
{
    use TimestampableTrait;

    protected mixed $id = null;

    protected ?ProductInterface $product = null;

    #[Assert\Valid]
    #[Assert\Count(min: 2, minMessage: 'sylius_product_bundle.product_bundle_item.min_count', groups: ['sylius_product_bundle'])]
    #[Groups(['shop:product:read', 'shop:product_bundle:read', 'admin:product_bundle:read', 'admin:product_bundle:create', 'admin:product_bundle:update', 'product_bundle:read', 'product_bundle:write'])]
    #[SerializedName('items')]
    protected Collection $productBundleItems;

    #[Groups(['admin:product_bundle:create', 'admin:product_bundle:update'])]
    #[SerializedName('isPacked')]
    protected bool $isPackedProduct = false;

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

<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;

trait ProductBundleOrderItemsAwareTrait
{
    /** @var ArrayCollection|ProductBundleOrderItemInterface[] */
    protected $productBundleOrderItems;

    protected function init(): void
    {
        $this->productBundleOrderItems = new ArrayCollection();
    }

    /** @return ProductBundleOrderItemInterface[]|ArrayCollection */
    public function getProductBundleOrderItems()
    {
        return $this->productBundleOrderItems;
    }

    public function addProductBundleOrderItem(ProductBundleOrderItemInterface $productBundleOrderItem): void
    {
        $this->productBundleOrderItems->add($productBundleOrderItem);
        $productBundleOrderItem->setOrderItem($this);
    }
}

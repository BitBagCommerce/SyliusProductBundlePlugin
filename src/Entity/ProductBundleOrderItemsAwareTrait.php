<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

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

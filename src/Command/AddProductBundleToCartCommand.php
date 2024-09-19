<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class AddProductBundleToCartCommand implements OrderIdentityAwareInterface, ProductCodeAwareInterface
{
    /** @var Collection<int, AddProductBundleItemToCartCommandInterface> */
    private Collection $productBundleItems;

    public function __construct(
        private readonly int $orderId,
        private readonly string $productCode,
        private readonly int $quantity = 1,
    ) {
        $this->productBundleItems = new ArrayCollection();
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /** @return Collection<int, AddProductBundleItemToCartCommandInterface> */
    public function getProductBundleItems(): Collection
    {
        return $this->productBundleItems;
    }

    /** @param Collection<int, AddProductBundleItemToCartCommandInterface> $productBundleItems */
    public function setProductBundleItems(Collection $productBundleItems): void
    {
        $this->productBundleItems = $productBundleItems;
    }
}

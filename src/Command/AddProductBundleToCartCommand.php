<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Command;

use Doctrine\Common\Collections\Collection;

final class AddProductBundleToCartCommand implements OrderIdentityAwareInterface, ProductCodeAwareInterface
{
    /** @var Collection<int, AddProductBundleItemToCartCommand> */
    private Collection $productBundleItems;

    public function __construct(
        private int $orderId,
        private string $productCode,
        private int $quantity = 1,
    ) {
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

    /** @return Collection<int, AddProductBundleItemToCartCommand> */
    public function getProductBundleItems(): Collection
    {
        return $this->productBundleItems;
    }

    /** @param Collection<int, AddProductBundleItemToCartCommand> $productBundleItems */
    public function setProductBundleItems(Collection $productBundleItems): void
    {
        $this->productBundleItems = $productBundleItems;
    }
}

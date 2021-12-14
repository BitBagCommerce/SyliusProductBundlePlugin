<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Command;

final class AddProductBundleToCartCommand implements ProductBundleIdAwareInterface
{
    /** @var string|null */
    private $orderToken;

    /** @var int|null */
    private $orderId;

    /** @var int|null */
    private $productBundleId;

    /** @var int */
    private $quantity;

    public function __construct(int $quantity = 1)
    {
        $this->quantity = $quantity;
    }

    public function getOrderToken(): ?string
    {
        return $this->orderToken;
    }

    public function setOrderToken(?string $orderTokenValue): void
    {
        $this->orderToken = $orderTokenValue;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(int $id): void
    {
        $this->orderId = $id;
    }

    public function getProductBundleId(): ?int
    {
        return $this->productBundleId;
    }

    public function setProductBundleId(int $id): void
    {
        $this->productBundleId = $id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}

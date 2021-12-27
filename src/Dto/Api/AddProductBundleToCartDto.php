<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Dto\Api;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

final class AddProductBundleToCartDto implements OrderTokenValueAwareInterface
{
    /** @var string */
    private $orderTokenValue;

    /** @var string */
    private $productCode;

    /** @var int */
    private $quantity;

    public function __construct(string $productCode, int $quantity = 1)
    {
        $this->productCode = $productCode;
        $this->quantity = $quantity;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}

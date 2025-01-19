<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Dto\Api;

use Sylius\Bundle\ApiBundle\Attribute\OrderTokenValueAware;
use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;

#[OrderTokenValueAware]
final class AddProductBundleToCartDto implements IriToIdentifierConversionAwareInterface
{
    public function __construct(
        private readonly string $productCode,
        private string $orderTokenValue,
        private readonly int $quantity = 1,
    ) {
    }

    public function getOrderTokenValue(): string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(string $orderTokenValue): void
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

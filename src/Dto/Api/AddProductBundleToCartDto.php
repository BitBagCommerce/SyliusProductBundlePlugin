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

namespace Sylius\ProductBundlePlugin\Dto\Api;

use Sylius\Bundle\ApiBundle\Attribute\OrderTokenValueAware;
use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[OrderTokenValueAware]
final class AddProductBundleToCartDto implements IriToIdentifierConversionAwareInterface
{
    public function __construct(
        #[Groups(['shop:cart:add_product_bundle'])]
        private readonly string $productCode,
        private string $orderTokenValue,
        #[Groups(['shop:cart:add_product_bundle'])]
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

<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Factory;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Dto\AddProductBundleToCartDtoInterface;

final class AddProductBundleToCartCommandFactory implements AddProductBundleToCartCommandFactoryInterface
{
    public function createNew(
        int $orderId,
        string $productCode,
        int $quantity
    ): AddProductBundleToCartCommand {
        return new AddProductBundleToCartCommand($orderId, $productCode, $quantity);
    }

    public function createFromDto(AddProductBundleToCartDtoInterface $dto): AddProductBundleToCartCommand
    {
        $cartId = $dto->getCart()->getId();
        $productCode = $dto->getProduct()->getCode() ?? '';
        $quantity = $dto->getCartItem()->getQuantity();

        return $this->createNew($cartId, $productCode, $quantity);
    }
}

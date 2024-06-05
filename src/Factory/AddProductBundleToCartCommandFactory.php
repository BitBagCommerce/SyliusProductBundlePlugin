<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
        int $quantity,
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

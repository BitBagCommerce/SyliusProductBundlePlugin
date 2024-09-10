<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Factory;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommand;
use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Dto\AddProductBundleToCartDtoInterface;
use Doctrine\Common\Collections\Collection;

final class AddProductBundleToCartCommandFactory implements AddProductBundleToCartCommandFactoryInterface
{
    /** @param Collection<int, AddProductBundleItemToCartCommand> $productBundleItems */
    public function createNew(
        int $orderId,
        string $productCode,
        int $quantity,
        Collection $productBundleItems,
    ): AddProductBundleToCartCommand {
        $command = new AddProductBundleToCartCommand($orderId, $productCode, $quantity);
        $command->setProductBundleItems($productBundleItems);

        return $command;
    }

    public function createFromDto(AddProductBundleToCartDtoInterface $dto): AddProductBundleToCartCommand
    {
        $cartId = $dto->getCart()->getId();
        $productCode = $dto->getProduct()->getCode() ?? '';
        $quantity = $dto->getCartItem()->getQuantity();
        $productBundleItems = $dto->getProductBundleItems();

        return $this->createNew($cartId, $productCode, $quantity, $productBundleItems);
    }
}

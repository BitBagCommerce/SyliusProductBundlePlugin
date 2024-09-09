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
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use Doctrine\Common\Collections\Collection;

interface AddProductBundleToCartCommandFactoryInterface
{
    /** @param Collection<int, ProductBundleOrderItemInterface> $productBundleItems */
    public function createNew(
        int $orderId,
        string $productCode,
        int $quantity,
        Collection $productBundleItems,
    ): AddProductBundleToCartCommand;

    public function createFromDto(AddProductBundleToCartDtoInterface $dto): AddProductBundleToCartCommand;
}

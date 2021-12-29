<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Factory;

use BitBag\SyliusProductBundlePlugin\Dto\AddProductBundleToCartDtoInterface;
use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Order\Model\OrderInterface;

interface AddProductBundleToCartDtoFactoryInterface
{
    public function createNew(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductInterface $product
    ): AddProductBundleToCartDtoInterface;
}

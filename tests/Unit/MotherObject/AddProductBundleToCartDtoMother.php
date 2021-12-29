<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject;

use BitBag\SyliusProductBundlePlugin\Dto\AddProductBundleToCartDto;
use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class AddProductBundleToCartDtoMother
{
    public static function create(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        array $productBundleItems
    ): AddProductBundleToCartDto {
        return new AddProductBundleToCartDto($order, $orderItem, $product, $productBundleItems);
    }
}
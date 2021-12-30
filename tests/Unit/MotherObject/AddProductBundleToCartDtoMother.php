<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject;

use BitBag\SyliusProductBundlePlugin\Dto\AddProductBundleToCartDto;

final class AddProductBundleToCartDtoMother
{
    public static function create(): AddProductBundleToCartDto
    {
        $order = OrderMother::create();
        $orderItem = OrderItemMother::create();
        $product = ProductMother::create();
        $productBundleItems = [];

        return new AddProductBundleToCartDto($order, $orderItem, $product, $productBundleItems);
    }

    public static function createWithOrderIdAndProductCode(int $orderId, string $productCode): AddProductBundleToCartDto
    {
        $order = OrderMother::createWithId($orderId);
        $orderItem = OrderItemMother::create();
        $product = ProductMother::createWithCode($productCode);
        $productBundleItems = [];

        return new AddProductBundleToCartDto($order, $orderItem, $product, $productBundleItems);
    }
}

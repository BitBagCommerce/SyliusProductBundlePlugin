<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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

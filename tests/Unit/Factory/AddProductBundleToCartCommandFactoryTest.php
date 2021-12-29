<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Factory;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Factory\AddProductBundleToCartCommandFactory;
use PHPUnit\Framework\TestCase;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\AddProductBundleToCartDtoMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\OrderItemMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\OrderMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductMother;

final class AddProductBundleToCartCommandFactoryTest extends TestCase
{
    public const ORDER_ID = 5;

    public const PRODUCT_CODE = 'MY_PRODUCT';

    public const QUANTITY = 2;

    public function testCreateAddProductBundleToCartCommandObject(): void
    {
        $factory = new AddProductBundleToCartCommandFactory();
        $command = $factory->createNew(self::ORDER_ID, self::PRODUCT_CODE, self::QUANTITY);

        self::assertInstanceOf(AddProductBundleToCartCommand::class, $command);
        self::assertEquals(self::ORDER_ID, $command->getOrderId());
        self::assertEquals(self::PRODUCT_CODE, $command->getProductCode());
        self::assertEquals(self::QUANTITY, $command->getQuantity());
    }

    public function testCreateAddProductBundleToCartCommandObjectFromDto(): void
    {
        $cart = OrderMother::createWithId(self::ORDER_ID);
        $orderItem = OrderItemMother::create();
        $product = ProductMother::createWithCode(self::PRODUCT_CODE);
        $bundleItems = [];
        $dto = AddProductBundleToCartDtoMother::create($cart, $orderItem, $product, $bundleItems);

        $factory = new AddProductBundleToCartCommandFactory();
        $command = $factory->createFromDto($dto);

        self::assertInstanceOf(AddProductBundleToCartCommand::class, $command);
        self::assertEquals(self::ORDER_ID, $command->getOrderId());
        self::assertEquals(self::PRODUCT_CODE, $command->getProductCode());
        self::assertEquals(0, $command->getQuantity());
    }
}

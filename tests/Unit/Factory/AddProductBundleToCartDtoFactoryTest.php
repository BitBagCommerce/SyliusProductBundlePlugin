<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Factory;

use BitBag\SyliusProductBundlePlugin\Dto\AddProductBundleToCartDtoInterface;
use BitBag\SyliusProductBundlePlugin\Factory\AddProductBundleItemToCartCommandFactoryInterface;
use BitBag\SyliusProductBundlePlugin\Factory\AddProductBundleToCartDtoFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\AddProductBundleItemToCartCommandMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\OrderItemMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\OrderMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductBundleItemMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductBundleMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductMother;

final class AddProductBundleToCartDtoFactoryTest extends TestCase
{
    /** @var AddProductBundleItemToCartCommandFactoryInterface|mixed|MockObject */
    private $addProductBundleItemToCartCommandFactory;

    protected function setUp(): void
    {
        $this->addProductBundleItemToCartCommandFactory = $this->createMock(
            AddProductBundleItemToCartCommandFactoryInterface::class,
        );
    }

    public function testCreateAddProductBundleToCartDtoObject(): void
    {
        $bundleItem1 = ProductBundleItemMother::create();
        $bundleItem2 = ProductBundleItemMother::create();
        $addProductBundleItemToCartCommand1 = AddProductBundleItemToCartCommandMother::create($bundleItem1);
        $addProductBundleItemToCartCommand2 = AddProductBundleItemToCartCommandMother::create($bundleItem2);

        $this->addProductBundleItemToCartCommandFactory->expects(self::exactly(2))
            ->method('createNew')
            ->withConsecutive([$bundleItem1], [$bundleItem2])
            ->willReturnOnConsecutiveCalls($addProductBundleItemToCartCommand1, $addProductBundleItemToCartCommand2)
        ;

        $factory = new AddProductBundleToCartDtoFactory($this->addProductBundleItemToCartCommandFactory);

        $order = OrderMother::create();
        $orderItem = OrderItemMother::create();
        $productBundle = ProductBundleMother::createWithBundleItems($bundleItem1, $bundleItem2);
        $product = ProductMother::createWithBundle($productBundle);
        $dto = $factory->createNew($order, $orderItem, $product);

        self::assertInstanceOf(AddProductBundleToCartDtoInterface::class, $dto);
        self::assertSame($order, $dto->getCart());
        self::assertSame($orderItem, $dto->getCartItem());
        self::assertSame($product, $dto->getProduct());
        self::assertCount(2, $dto->getProductBundleItems());
    }
}

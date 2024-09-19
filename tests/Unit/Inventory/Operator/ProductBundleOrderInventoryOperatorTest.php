<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Inventory\Operator;

use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\Inventory\Operator\ProductBundleOrderInventoryOperator;
use BitBag\SyliusProductBundlePlugin\Inventory\Operator\ProductBundleOrderInventoryOperatorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductBundleOrderInventoryOperatorTest extends TestCase
{
    private ProductInterface|MockObject $regularProduct;

    private ProductInterface|MockObject $bundle;

    private ProductBundleOrderInventoryOperatorInterface $operator;

    public function setUp(): void
    {
        $this->bundle = $this->createMock(ProductInterface::class);
        $this->bundle
            ->method('isBundle')
            ->willReturn(true);

        $this->regularProduct = $this->createMock(ProductInterface::class);
        $this->regularProduct
            ->method('isBundle')
            ->willReturn(false);

        $this->operator = new ProductBundleOrderInventoryOperator();
    }

    public function testItHolds(): void
    {
        $bundleVariant1 = $this->createMock(ProductVariantInterface::class);
        $bundleVariant1
            ->method('isTracked')
            ->willReturn(true);
        $bundleVariant1
            ->method('getOnHold')
            ->willReturn(10);
        $bundleVariant1
            ->method('setOnHold')
            ->with(14);

        $bundleVariant2 = $this->createMock(ProductVariantInterface::class);
        $bundleVariant2
            ->method('isTracked')
            ->willReturn(true);
        $bundleVariant2
            ->method('getOnHold')
            ->willReturn(20);
        $bundleVariant2
            ->method('setOnHold')
            ->with(26);

        $bundleOrderItem = $this->createMock(OrderItemInterface::class);
        $bundleOrderItem
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($this->bundle);

        $bundleOrderItem
            ->expects(self::exactly(2))
            ->method('getQuantity')
            ->willReturn(2);

        $bundleOrderItem
            ->expects(self::once())
            ->method('getProductBundleOrderItems')
            ->willReturn([
                $this->mockProductBundleOrderItem($bundleVariant1, 2),
                $this->mockProductBundleOrderItem($bundleVariant2, 3),
            ]);
        $bundleOrderItem->expects(self::never())->method('getVariant');

        $regularOrderItem = $this->createMock(OrderItemInterface::class);
        $regularOrderItem
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($this->regularProduct);
        $regularOrderItem
            ->expects(self::once())
            ->method('getQuantity')
            ->willReturn(3);

        $variant = $this->createMock(ProductVariantInterface::class);
        $variant
            ->method('isTracked')
            ->willReturn(true);
        $variant
            ->method('getOnHold')
            ->willReturn(100);
        $variant
            ->method('setOnHold')
            ->with(103);

        $regularOrderItem
            ->expects(self::once())
            ->method('getVariant')
            ->willReturn($variant);

        $regularOrderItem->expects(self::never())->method('getProductBundleOrderItems');

        $order = $this->mockOrder(
            $regularOrderItem,
            $bundleOrderItem,
        );

        $this->operator->hold($order);
    }

    public function testItSells(): void
    {
        $bundleVariant1 = $this->createMock(ProductVariantInterface::class);
        $bundleVariant1
            ->method('isTracked')
            ->willReturn(true);
        $bundleVariant1
            ->method('getOnHold')
            ->willReturn(10);
        $bundleVariant1
            ->method('getOnHand')
            ->willReturn(20);
        $bundleVariant1
            ->method('setOnHold')
            ->with(6);
        $bundleVariant1
            ->method('setOnHand')
            ->with(16);

        $bundleOrderItem = $this->createMock(OrderItemInterface::class);
        $bundleOrderItem
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($this->bundle);

        $bundleOrderItem
            ->expects(self::once())
            ->method('getQuantity')
            ->willReturn(2);

        $bundleOrderItem
            ->expects(self::once())
            ->method('getProductBundleOrderItems')
            ->willReturn([
                $this->mockProductBundleOrderItem($bundleVariant1, 2),
            ]);
        $bundleOrderItem->expects(self::never())->method('getVariant');

        $regularOrderItem = $this->createMock(OrderItemInterface::class);
        $regularOrderItem
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($this->regularProduct);
        $regularOrderItem
            ->expects(self::once())
            ->method('getQuantity')
            ->willReturn(3);

        $variant = $this->createMock(ProductVariantInterface::class);
        $variant
            ->method('isTracked')
            ->willReturn(true);
        $variant
            ->method('getOnHold')
            ->willReturn(100);
        $variant
            ->method('setOnHold')
            ->with(97);
        $variant
            ->method('getOnHand')
            ->willReturn(200);
        $variant
            ->method('setOnHand')
            ->with(197);

        $regularOrderItem
            ->expects(self::once())
            ->method('getVariant')
            ->willReturn($variant);

        $regularOrderItem->expects(self::never())->method('getProductBundleOrderItems');

        $order = $this->mockOrder(
            $regularOrderItem,
            $bundleOrderItem,
        );

        $this->operator->sell($order);
    }

    public function testItReleases(): void
    {
        $bundleVariant1 = $this->createMock(ProductVariantInterface::class);
        $bundleVariant1
            ->method('isTracked')
            ->willReturn(true);
        $bundleVariant1
            ->method('getOnHold')
            ->willReturn(10);
        $bundleVariant1
            ->method('setOnHold')
            ->with(6);

        $bundleOrderItem = $this->createMock(OrderItemInterface::class);
        $bundleOrderItem
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($this->bundle);

        $bundleOrderItem
            ->expects(self::once())
            ->method('getQuantity')
            ->willReturn(2);

        $bundleOrderItem
            ->expects(self::once())
            ->method('getProductBundleOrderItems')
            ->willReturn([
                $this->mockProductBundleOrderItem($bundleVariant1, 2),
            ]);
        $bundleOrderItem->expects(self::never())->method('getVariant');

        $regularOrderItem = $this->createMock(OrderItemInterface::class);
        $regularOrderItem
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($this->regularProduct);
        $regularOrderItem
            ->expects(self::once())
            ->method('getQuantity')
            ->willReturn(3);

        $variant = $this->createMock(ProductVariantInterface::class);
        $variant
            ->method('isTracked')
            ->willReturn(true);
        $variant
            ->method('getOnHold')
            ->willReturn(100);
        $variant
            ->method('setOnHold')
            ->with(97);

        $regularOrderItem
            ->expects(self::once())
            ->method('getVariant')
            ->willReturn($variant);

        $regularOrderItem->expects(self::never())->method('getProductBundleOrderItems');

        $order = $this->mockOrder(
            $regularOrderItem,
            $bundleOrderItem,
        );

        $this->operator->release($order);
    }

    public function testItGivesBack(): void
    {
        $bundleVariant1 = $this->createMock(ProductVariantInterface::class);
        $bundleVariant1
            ->method('isTracked')
            ->willReturn(true);
        $bundleVariant1
            ->method('getOnHand')
            ->willReturn(10);
        $bundleVariant1
            ->method('setOnHand')
            ->with(14);

        $bundleOrderItem = $this->createMock(OrderItemInterface::class);
        $bundleOrderItem
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($this->bundle);

        $bundleOrderItem
            ->expects(self::once())
            ->method('getQuantity')
            ->willReturn(2);

        $bundleOrderItem
            ->expects(self::once())
            ->method('getProductBundleOrderItems')
            ->willReturn([
                $this->mockProductBundleOrderItem($bundleVariant1, 2),
            ]);
        $bundleOrderItem->expects(self::never())->method('getVariant');

        $regularOrderItem = $this->createMock(OrderItemInterface::class);
        $regularOrderItem
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($this->regularProduct);
        $regularOrderItem
            ->expects(self::once())
            ->method('getQuantity')
            ->willReturn(3);

        $variant = $this->createMock(ProductVariantInterface::class);
        $variant
            ->method('isTracked')
            ->willReturn(true);
        $variant
            ->method('getOnHand')
            ->willReturn(100);
        $variant
            ->method('setOnHand')
            ->with(103);

        $regularOrderItem
            ->expects(self::once())
            ->method('getVariant')
            ->willReturn($variant);

        $regularOrderItem->expects(self::never())->method('getProductBundleOrderItems');

        $order = $this->mockOrder(
            $regularOrderItem,
            $bundleOrderItem,
        );

        $this->operator->giveBack($order);
    }

    private function mockProductBundleOrderItem(
        ProductVariantInterface $variant,
        int $quantity,
    ): ProductBundleOrderItemInterface {
        $bundleOrderItem = $this->createMock(ProductBundleOrderItemInterface::class);
        $bundleOrderItem
            ->method('getProductVariant')
            ->willReturn($variant);
        $bundleOrderItem
            ->method('getQuantity')
            ->willReturn($quantity);

        return $bundleOrderItem;
    }

    private function mockOrder(OrderItemInterface ...$items): OrderInterface
    {
        $order = $this->createMock(OrderInterface::class);
        $order
            ->method('getItems')
            ->willReturn(new ArrayCollection($items));

        return $order;
    }
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Inventory\Checker;

use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Inventory\Checker\ProductBundleOrderItemAvailabilityChecker;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductBundleOrderItemAvailabilityCheckerTest extends TestCase
{
    /** @dataProvider provideAreOrderedBundledProductVariantsAvailable */
    public function testAreOrderedBundledProductVariantsAvailable(
        OrderItemInterface $orderItem,
        bool $result,
    ): void {
        $checker = new ProductBundleOrderItemAvailabilityChecker();
        self::assertEquals($result, $checker->areOrderedBundledProductVariantsAvailable($orderItem));
    }

    public function provideAreOrderedBundledProductVariantsAvailable(): array
    {
        $unTrackedVariant = $this->mockProductVariant(false, 0, 0);
        $unTrackedBundleOrderItem = $this->mockBundleOrderItem($unTrackedVariant, 2);
        $orderItem1 = $this->mockOrderItem([$unTrackedBundleOrderItem], 2);

        $trackedVariantOutOfStock = $this->mockProductVariant(true, 0, 0);
        $trackedOutOfStockBundleOrderItem = $this->mockBundleOrderItem($trackedVariantOutOfStock, 2);
        $orderItem2 = $this->mockOrderItem([$trackedOutOfStockBundleOrderItem], 2);

        $trackedVariantInStock = $this->mockProductVariant(true, 10, 20);
        $trackedInStockBundleOrderItem = $this->mockBundleOrderItem($trackedVariantInStock, 2);
        $orderItem3 = $this->mockOrderItem([$trackedOutOfStockBundleOrderItem, $trackedInStockBundleOrderItem], 2);

        $orderItem4 = $this->mockOrderItem([$trackedInStockBundleOrderItem], 5);

        $trackedVariantInStock2 = $this->mockProductVariant(true, 20, 10);
        $trackedInStockBundleOrderItem2 = $this->mockBundleOrderItem($trackedVariantInStock2, 2);
        $orderItem5 = $this->mockOrderItem([$trackedInStockBundleOrderItem2], 5);

        $trackedVariantInStock3 = $this->mockProductVariant(true, 10, 9);
        $trackedInStockBundleOrderItem3 = $this->mockBundleOrderItem($trackedVariantInStock3, 2);
        $orderItem6 = $this->mockOrderItem([$trackedInStockBundleOrderItem3], 5);

        $trackedVariantInStock4 = $this->mockProductVariant(true, 9, 10);
        $trackedInStockBundleOrderItem4 = $this->mockBundleOrderItem($trackedVariantInStock4, 2);
        $orderItem7 = $this->mockOrderItem([$trackedInStockBundleOrderItem4], 5);

        return [
            'untracked variant' => [
                $orderItem1,
                true,
            ],
            'variant out of stock' => [
                $orderItem2,
                false,
            ],
            'one variant out of stock, one in stock' => [
                $orderItem3,
                false,
            ],
            'on-hold edge case' => [
                $orderItem4,
                true,
            ],
            'on-hand edge case' => [
                $orderItem5,
                true,
            ],
            'on-hold insufficient' => [
                $orderItem6,
                false,
            ],
            'on-hand insufficient' => [
                $orderItem6,
                false,
            ],
        ];
    }

    private function mockProductVariant(bool $isTracked, int $onHold, int $onHand): ProductVariantInterface
    {
        $variant = $this->createMock(ProductVariantInterface::class);
        $variant
            ->method('isTracked')
            ->willReturn($isTracked);
        $variant
            ->method('getOnHold')
            ->willReturn($onHold);
        $variant
            ->method('getOnHand')
            ->willReturn($onHand);

        return $variant;
    }

    private function mockBundleOrderItem(ProductVariantInterface $variant, int $quantity): ProductBundleOrderItemInterface
    {
        $item = $this->createMock(ProductBundleOrderItemInterface::class);
        $item
            ->method('getProductVariant')
            ->willReturn($variant);
        $item
            ->method('getQuantity')
            ->willReturn($quantity);

        return $item;
    }

    /** @param ProductBundleOrderItemInterface[] $bundleOrderItems */
    private function mockOrderItem(array $bundleOrderItems, int $quantity): OrderItemInterface
    {
        $orderItem = $this->createMock(OrderItemInterface::class);
        $orderItem
            ->method('getProductBundleOrderItems')
            ->willReturn($bundleOrderItems);
        $orderItem
            ->method('getQuantity')
            ->willReturn($quantity);

        return $orderItem;
    }
}

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
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\Inventory\Checker\FeatureFlagCheckerInterface;
use BitBag\SyliusProductBundlePlugin\Inventory\Checker\OrderItemAvailabilityChecker;
use BitBag\SyliusProductBundlePlugin\Inventory\Checker\ProductBundleOrderItemAvailabilityCheckerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Inventory\Checker\OrderItemAvailabilityCheckerInterface;

final class OrderItemAvailabilityCheckerTest extends TestCase
{
    private OrderItemAvailabilityCheckerInterface|MockObject $decorated;

    private FeatureFlagCheckerInterface|MockObject $featureFlagChecker;

    private ProductBundleOrderItemAvailabilityCheckerInterface|MockObject $bundleOrderItemAvailabilityChecker;

    private OrderItemAvailabilityCheckerInterface $checker;

    public function setUp(): void
    {
        $this->decorated = $this->createMock(OrderItemAvailabilityCheckerInterface::class);
        $this->featureFlagChecker = $this->createMock(FeatureFlagCheckerInterface::class);
        $this->bundleOrderItemAvailabilityChecker = $this->createMock(ProductBundleOrderItemAvailabilityCheckerInterface::class);

        $this->checker = new OrderItemAvailabilityChecker(
            $this->decorated,
            $this->featureFlagChecker,
            $this->bundleOrderItemAvailabilityChecker,
        );
    }

    public function testItCallsDecoratedIfFeatureDisabled(): void
    {
        $this->featureFlagChecker
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->decorated
            ->expects(self::once())
            ->method('isReservedStockSufficient');

        $this->bundleOrderItemAvailabilityChecker->expects(self::never())->method(self::anything());

        $this->checker->isReservedStockSufficient($this->createMock(OrderItemInterface::class));
    }

    public function testItCallsDecoratedIfProductIsNotBundle(): void
    {
        $this->featureFlagChecker
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->decorated
            ->expects(self::once())
            ->method('isReservedStockSufficient');

        $this->bundleOrderItemAvailabilityChecker->expects(self::never())->method(self::anything());

        $product = $this->createMock(ProductInterface::class);
        $product
            ->expects(self::once())
            ->method('isBundle')
            ->willReturn(false);

        $orderItem = $this->createMock(OrderItemInterface::class);
        $orderItem
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($product);

        $this->checker->isReservedStockSufficient($orderItem);
    }

    public function testItCallsProductBundleCheckerIfProductIsBundle(): void
    {
        $this->featureFlagChecker
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->decorated->expects(self::never())->method(self::anything());

        $product = $this->createMock(ProductInterface::class);
        $product
            ->expects(self::once())
            ->method('isBundle')
            ->willReturn(true);

        $orderItem = $this->createMock(OrderItemInterface::class);
        $orderItem
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($product);

        $this->bundleOrderItemAvailabilityChecker
            ->expects(self::once())
            ->method('areOrderedBundledProductVariantsAvailable')
            ->with($orderItem);

        $this->checker->isReservedStockSufficient($orderItem);
    }
}

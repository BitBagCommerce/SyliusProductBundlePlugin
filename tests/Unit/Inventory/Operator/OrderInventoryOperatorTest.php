<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Inventory\Operator;

use BitBag\SyliusProductBundlePlugin\Inventory\Checker\FeatureFlagCheckerInterface;
use BitBag\SyliusProductBundlePlugin\Inventory\Operator\OrderInventoryOperator;
use BitBag\SyliusProductBundlePlugin\Inventory\Operator\ProductBundleOrderInventoryOperatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface as SyliusOrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;

final class OrderInventoryOperatorTest extends TestCase
{
    private OrderInventoryOperator $orderInventoryOperator;

    private SyliusOrderInventoryOperatorInterface|MockObject $decorated;

    private EntityManagerInterface|MockObject $productVariantManager;

    private FeatureFlagCheckerInterface|MockObject $featureFlagChecker;

    private ProductBundleOrderInventoryOperatorInterface|MockObject $productBundleOrderInventoryOperator;

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(SyliusOrderInventoryOperatorInterface::class);
        $this->productVariantManager = $this->createMock(EntityManagerInterface::class);
        $this->featureFlagChecker = $this->createMock(FeatureFlagCheckerInterface::class);
        $this->productBundleOrderInventoryOperator = $this->createMock(ProductBundleOrderInventoryOperatorInterface::class);

        $this->orderInventoryOperator = new OrderInventoryOperator(
            $this->decorated,
            $this->productVariantManager,
            $this->featureFlagChecker,
            $this->productBundleOrderInventoryOperator,
        );
    }

    public function testCancelCallsDecoratedIfFeatureFlagDisabled(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->featureFlagChecker
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->decorated
            ->expects(self::once())
            ->method('cancel')
            ->with($order);

        $this->orderInventoryOperator->cancel($order);
    }

    public function testCancelGivesBackInventoryIfOrderPaidOrRefunded(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $order->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_PAID);

        $this->featureFlagChecker
            ->method('isEnabled')
            ->willReturn(true);

        $this->productBundleOrderInventoryOperator
            ->expects(self::once())
            ->method('giveBack')
            ->with($order);

        $this->orderInventoryOperator->cancel($order);
    }

    public function testCancelReleasesInventoryIfNotPaidOrRefunded(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $order->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_CART);

        $this->featureFlagChecker
            ->method('isEnabled')
            ->willReturn(true);

        $this->productBundleOrderInventoryOperator
            ->expects(self::once())
            ->method('release')
            ->with($order);

        $this->orderInventoryOperator->cancel($order);
    }

    public function testHoldCallsDecoratedIfFeatureFlagDisabled(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->featureFlagChecker
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->decorated
            ->expects(self::once())
            ->method('hold')
            ->with($order);

        $this->orderInventoryOperator->hold($order);
    }

    public function testHoldHandlesBundleIfFeatureFlagEnabled(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->decorated->expects(self::never())->method(self::anything());

        $this->featureFlagChecker
            ->method('isEnabled')
            ->willReturn(true);

        $this->productBundleOrderInventoryOperator
            ->expects(self::once())
            ->method('hold')
            ->with($order);

        $this->orderInventoryOperator->hold($order);
    }

    public function testSellCallsDecoratedIfFeatureFlagDisabled(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->featureFlagChecker
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->decorated
            ->expects(self::once())
            ->method('sell')
            ->with($order);

        $this->orderInventoryOperator->sell($order);
    }

    public function testSellHandlesBundleIfFeatureFlagEnabled(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->decorated->expects(self::never())->method(self::anything());

        $this->featureFlagChecker
            ->method('isEnabled')
            ->willReturn(true);

        $this->productBundleOrderInventoryOperator
            ->expects(self::once())
            ->method('sell')
            ->with($order);

        $this->orderInventoryOperator->sell($order);
    }
}

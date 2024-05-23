<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Decorator;

use BitBag\SyliusProductBundlePlugin\Doctrine\ORM\Inventory\Operator\OrderInventoryOperator as OrderInventoryOperatorDecorator;
use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperator;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\OrderPaymentStates;

class OrderInventoryOperatorTest extends TestCase
{
    private const FIRST_BUNDLED_PRODUCT_VARIANT_NAME = 'First Bundled Product Variant';

    private MockObject|OrderInterface $order;
    private MockObject|ProductVariantInterface $productBundleVariant;
    private MockObject|ProductVariantInterface $productVariant;
    private MockObject|ProductBundleOrderItemInterface $firstProductBundleOrderItem;
    private MockObject|ProductBundleOrderItemInterface $secondProductBundleOrderItem;
    private MockObject|ProductVariantInterface $firstProductBundleItemVariant;
    private MockObject|ProductVariantInterface $secondProductBundleItemVariant;

    private OrderInventoryOperator $decorated;
    private OrderInventoryOperatorDecorator $orderInventoryOperator;

    public function OrderPaymentStatesProvider(): array
    {
        return [
            [OrderPaymentStates::STATE_PAID],
            [OrderPaymentStates::STATE_REFUNDED]
        ];
    }

    protected function setUp(): void
    {
        $this->order = $this->createMock(OrderInterface::class);
        $this->productBundleVariant = $this->createMock(ProductVariantInterface::class);
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->firstProductBundleOrderItem = $this->createMock(ProductBundleOrderItemInterface::class);
        $this->secondProductBundleOrderItem = $this->createMock(ProductBundleOrderItemInterface::class);
        $this->firstProductBundleItemVariant = $this->createMock(ProductVariantInterface::class);
        $this->secondProductBundleItemVariant = $this->createMock(ProductVariantInterface::class);

        $productBundle = $this->createMock(OrderItemInterface::class);
        $product = $this->createMock(OrderItemInterface::class);

        $this->order
            ->method('getItems')
            ->willReturn(new ArrayCollection([$productBundle, $product]));

        $productBundle
            ->method('getQuantity')
            ->willReturn(1);

        $productBundle
            ->method('getVariant')
            ->willReturn($this->productBundleVariant);

        $productBundle
            ->method('getProductBundleOrderItems')
            ->willReturn(new ArrayCollection([$this->firstProductBundleOrderItem, $this->secondProductBundleOrderItem]));

        $product
            ->method('getQuantity')
            ->willReturn(1);

        $product
            ->method('getVariant')
            ->willReturn($this->productVariant);

        $product
            ->method('getProductBundleOrderItems')
            ->willReturn(new ArrayCollection([]));

        $this->productBundleVariant
            ->method('isTracked')
            ->willReturn(true);

        $this->productBundleVariant
            ->method('getOnHold')
            ->willReturn(1);

        $this->productBundleVariant
            ->method('getOnHand')
            ->willReturn(10);

        $this->productVariant
            ->method('isTracked')
            ->willReturn(true);

        $this->productVariant
            ->method('getOnHold')
            ->willReturn(1);

        $this->productVariant
            ->method('getOnHand')
            ->willReturn(10);

        $this->firstProductBundleItemVariant
            ->method('isTracked')
            ->willReturn(true);

        $this->firstProductBundleOrderItem
            ->method('getProductVariant')
            ->willReturn($this->firstProductBundleItemVariant);

        $this->secondProductBundleItemVariant
            ->method('isTracked')
            ->willReturn(true);

        $this->secondProductBundleOrderItem
            ->method('getProductVariant')
            ->willReturn($this->secondProductBundleItemVariant);

        $this->decorated = new OrderInventoryOperator();
    }

    /** @dataProvider OrderPaymentStatesProvider */
    public function testDoesNotUpdateBundledProductsStockOnCancelIfEnvVariableIsFalse(string $orderPaymentState): void
    {
        $this->orderInventoryOperator = new OrderInventoryOperatorDecorator($this->decorated, false);
        $this->order
            ->method('getPaymentState')
            ->willReturn($orderPaymentState);

        $this->productBundleVariant->expects($this->never())->method('setOnHold');
        $this->productBundleVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(11));

        $this->productVariant->expects($this->never())->method('setOnHold');
        $this->productVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(11));

        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->orderInventoryOperator->cancel($this->order);
    }

    public function testDoesNotUpdateBundledProductsStockOnCancelNotPaidNotRefundedOrderIfEnvVariableIsFalse(): void
    {
        $this->orderInventoryOperator = new OrderInventoryOperatorDecorator($this->decorated, false);
        $this->order
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);

        $this->productBundleVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(0));
        $this->productBundleVariant->expects($this->never())->method('setOnHand');

        $this->productVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(0));
        $this->productVariant->expects($this->never())->method('setOnHand');

        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->orderInventoryOperator->cancel($this->order);
    }

    public function testDoesNotUpdateBundledProductsStockOnHoldIfEnvVariableIsFalse(): void
    {
        $this->orderInventoryOperator = new OrderInventoryOperatorDecorator($this->decorated, false);

        $this->productBundleVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(2));
        $this->productBundleVariant->expects($this->never())->method('setOnHand');

        $this->productVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(2));
        $this->productVariant->expects($this->never())->method('setOnHand');

        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->orderInventoryOperator->hold($this->order);
    }

    public function testDoesNotUpdateBundledProductsStockOnSellIfEnvVariableIsFalse(): void
    {
        $this->orderInventoryOperator = new OrderInventoryOperatorDecorator($this->decorated, false);

        $this->productBundleVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(0));
        $this->productBundleVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(9));

        $this->productVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(0));
        $this->productVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(9));

        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->orderInventoryOperator->sell($this->order);
    }

    /** @dataProvider OrderPaymentStatesProvider */
    public function testUpdatesBundledProductsStockOnCancelIfEnvVariableIsTrue(string $orderPaymentState): void
    {
        $this->setMocks(
            firstProductBundleItemVariant: ['onHold' => 1, 'onHand' => 10],
            secondProductBundleItemVariant: ['onHold' => 2, 'onHand' => 10],
            firstProductBundleOrderItem: ['quantity' => 1],
            secondProductBundleOrderItem: ['quantity' => 2],
        );

        $this->orderInventoryOperator = new OrderInventoryOperatorDecorator($this->decorated, true);
        $this->order
            ->method('getPaymentState')
            ->willReturn($orderPaymentState);

        $this->productBundleVariant->expects($this->never())->method('setOnHold');
        $this->productBundleVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(11));

        $this->productVariant->expects($this->never())->method('setOnHold');
        $this->productVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(11));

        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->firstProductBundleItemVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(11));

        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->secondProductBundleItemVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(12));

        $this->orderInventoryOperator->cancel($this->order);
    }

    public function testUpdatesBundledProductsStockOnSellIfEnvVariableIsTrue(): void
    {
        $this->setMocks(
            firstProductBundleItemVariant: ['onHold' => 1, 'onHand' => 10],
            secondProductBundleItemVariant: ['onHold' => 2, 'onHand' => 10],
            firstProductBundleOrderItem: ['quantity' => 1],
            secondProductBundleOrderItem: ['quantity' => 2],
        );

        $this->orderInventoryOperator = new OrderInventoryOperatorDecorator($this->decorated, true);

        $this->productBundleVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(0));
        $this->productBundleVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(9));

        $this->productVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(0));
        $this->productVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(9));

        $this->firstProductBundleItemVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(0));
        $this->firstProductBundleItemVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(9));

        $this->secondProductBundleItemVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(0));
        $this->secondProductBundleItemVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(8));

        $this->orderInventoryOperator->sell($this->order);
    }

    public function testThrowsErrorOnCancelNotPaidNotRefundedOrderIfBundledProductsOnHoldTriesToGoBelowZero(): void
    {
        $this->setMocks(
            firstProductBundleItemVariant: ['onHold' => 1, 'onHand' => 10],
            secondProductBundleItemVariant: ['onHold' => 2, 'onHand' => 10],
            firstProductBundleOrderItem: ['quantity' => 2],
            secondProductBundleOrderItem: ['quantity' => 3],
        );

        $this->firstProductBundleItemVariant
            ->method('getName')
            ->willReturn(self::FIRST_BUNDLED_PRODUCT_VARIANT_NAME);

        $this->orderInventoryOperator = new OrderInventoryOperatorDecorator($this->decorated, true);
        $this->order
            ->method('getPaymentState')
            ->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);

        $this->productBundleVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(0));
        $this->productBundleVariant->expects($this->never())->method('setOnHand');

        $this->expectExceptionMessage(sprintf(
            'Not enough units to decrease on hold quantity from the inventory of a variant "%s".',
            self::FIRST_BUNDLED_PRODUCT_VARIANT_NAME,
        ));

        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->orderInventoryOperator->cancel($this->order);
    }

    public function testThrowsErrorOnSellIfBundledProductOnHoldTriesToGoBelowZero(): void
    {
        $this->setMocks(
            firstProductBundleItemVariant: ['onHold' => 1, 'onHand' => 10],
            secondProductBundleItemVariant: ['onHold' => 2, 'onHand' => 10],
            firstProductBundleOrderItem: ['quantity' => 2],
            secondProductBundleOrderItem: ['quantity' => 3],
        );

        $this->firstProductBundleItemVariant
            ->method('getName')
            ->willReturn(self::FIRST_BUNDLED_PRODUCT_VARIANT_NAME);

        $this->orderInventoryOperator = new OrderInventoryOperatorDecorator($this->decorated, true);

        $this->productBundleVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(0));
        $this->productBundleVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(9));

        $this->expectExceptionMessage(sprintf(
            'Not enough units to decrease on hold quantity from the inventory of a variant "%s".',
            self::FIRST_BUNDLED_PRODUCT_VARIANT_NAME,
        ));

        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->orderInventoryOperator->sell($this->order);
    }

    public function testThrowsErrorOnSellIfBundledProductOnHandTriesToGoBelowZero(): void
    {
        $this->setMocks(
            firstProductBundleItemVariant: ['onHold' => 10, 'onHand' => 0],
            secondProductBundleItemVariant: ['onHold' => 2, 'onHand' => 10],
            firstProductBundleOrderItem: ['quantity' => 1],
            secondProductBundleOrderItem: ['quantity' => 3],
        );

        $this->firstProductBundleItemVariant
            ->method('getName')
            ->willReturn(self::FIRST_BUNDLED_PRODUCT_VARIANT_NAME);

        $this->orderInventoryOperator = new OrderInventoryOperatorDecorator($this->decorated, true);

        $this->productBundleVariant->expects($this->once())->method('setOnHold')->with($this->identicalTo(0));
        $this->productBundleVariant->expects($this->once())->method('setOnHand')->with($this->identicalTo(9));

        $this->expectExceptionMessage(sprintf(
            'Not enough units to decrease on hand quantity from the inventory of a variant "%s".',
            self::FIRST_BUNDLED_PRODUCT_VARIANT_NAME,
        ));

        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->firstProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHold');
        $this->secondProductBundleItemVariant->expects($this->never())->method('setOnHand');

        $this->orderInventoryOperator->sell($this->order);
    }

    private function setMocks(
        array $firstProductBundleItemVariant = [],
        array $secondProductBundleItemVariant = [],
        array $firstProductBundleOrderItem = [],
        array $secondProductBundleOrderItem = []
    ): void {
        if ([] !== $firstProductBundleItemVariant) {
            $this->firstProductBundleItemVariant
                ->method('getOnHold')
                ->willReturn($firstProductBundleItemVariant['onHold']);

            $this->firstProductBundleItemVariant
                ->method('getOnHand')
                ->willReturn($firstProductBundleItemVariant['onHand']);
        }

        if ([] !== $secondProductBundleItemVariant) {
            $this->secondProductBundleItemVariant
                ->method('getOnHold')
                ->willReturn($secondProductBundleItemVariant['onHold']);

            $this->secondProductBundleItemVariant
                ->method('getOnHand')
                ->willReturn($secondProductBundleItemVariant['onHand']);
        }

        if ([] !== $firstProductBundleOrderItem) {
            $this->firstProductBundleOrderItem
                ->method('getQuantity')
                ->willReturn($firstProductBundleOrderItem['quantity']);
        }

        if ([] !== $secondProductBundleOrderItem) {
            $this->secondProductBundleOrderItem
                ->method('getQuantity')
                ->willReturn($secondProductBundleOrderItem['quantity']);
        }
    }
}
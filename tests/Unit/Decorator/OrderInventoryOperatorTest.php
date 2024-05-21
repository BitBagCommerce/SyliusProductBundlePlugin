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
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperator;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\OrderPaymentStates;

class OrderInventoryOperatorTest extends TestCase
{
    private MockObject|OrderInterface $order;
    private MockObject|OrderItemInterface $productBundle;
    private MockObject|ProductVariantInterface $productBundleVariant;
    private MockObject|OrderItemInterface $product;
    private MockObject|ProductVariantInterface $productVariant;
    private MockObject|ProductBundleItemInterface $firstProductBundleItem;
    private MockObject|ProductBundleItemInterface $secondProductBundleItem;
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
        $this->productBundle = $this->createMock(OrderItemInterface::class);
        $this->product = $this->createMock(OrderItemInterface::class);
        $this->productBundleVariant = $this->createMock(ProductVariantInterface::class);
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->firstProductBundleItem = $this->createMock(ProductBundleItemInterface::class);
        $this->secondProductBundleItem = $this->createMock(ProductBundleItemInterface::class);
        $this->firstProductBundleItemVariant = $this->createMock(ProductVariantInterface::class);
        $this->secondProductBundleItemVariant = $this->createMock(ProductVariantInterface::class);

        $this->order
            ->method('getItems')
            ->willReturn(new ArrayCollection([$this->productBundle, $this->product]));

        $this->productBundle
            ->method('getQuantity')
            ->willReturn(1);

        $this->productBundle
            ->method('getVariant')
            ->willReturn($this->productBundleVariant);

        $this->productBundle
            ->method('getProductBundleOrderItems')
            ->willReturn(new ArrayCollection([$this->firstProductBundleItem, $this->secondProductBundleItem]));

        $this->product
            ->method('getQuantity')
            ->willReturn(1);

        $this->product
            ->method('getVariant')
            ->willReturn($this->productVariant);

        $this->product
            ->method('getProductBundleOrderItems')
            ->willReturn(new ArrayCollection([]));

        $this->productBundleVariant
            ->method('isTracked')
            ->willReturn(true);

        $this->productBundleVariant
            ->method('getOnHold') // reserved
            ->willReturn(1);

        $this->productBundleVariant
            ->method('getOnHand') // available
            ->willReturn(10);

        $this->productVariant
            ->method('isTracked')
            ->willReturn(true);

        $this->productVariant
            ->method('getOnHold') // reserved
            ->willReturn(1);

        $this->productVariant
            ->method('getOnHand') // available
            ->willReturn(10);

        $this->firstProductBundleItemVariant
            ->method('isTracked')
            ->willReturn(true);

        $this->firstProductBundleItemVariant
            ->method('getOnHold')
            ->willReturn(1);

        $this->firstProductBundleItemVariant
            ->method('getOnHand')
            ->willReturn(10);

        $this->firstProductBundleItem
            ->method('getProductVariant')
            ->willReturn($this->firstProductBundleItemVariant);

        $this->firstProductBundleItem
            ->method('getQuantity')
            ->willReturn(1);

        $this->secondProductBundleItemVariant
            ->method('isTracked')
            ->willReturn(true);

        $this->secondProductBundleItemVariant
            ->method('getOnHold')
            ->willReturn(2);

        $this->secondProductBundleItemVariant
            ->method('getOnHand')
            ->willReturn(10);

        $this->secondProductBundleItem
            ->method('getProductVariant')
            ->willReturn($this->secondProductBundleItemVariant);

        $this->secondProductBundleItem
            ->method('getQuantity')
            ->willReturn(2);

        $this->decorated = new OrderInventoryOperator();
    }

    /** @dataProvider OrderPaymentStatesProvider */
    public function testDoesNotUpdateBundledProductsStockOnCancelIfEnvVariableIsSetToFalse(string $orderPaymentState): void
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

    public function testDoesNotUpdateBundledProductsStockOnCancelNotPaidNotRefundedOrderIfEnvVariableIsSetToFalse(): void
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

    public function testDoesNotUpdateBundledProductsStockOnHoldIfEnvVariableIsSetToFalse(): void
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

    public function testDoesNotUpdateBundledProductsStockOnSellIfEnvVariableIsSetToFalse(): void
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
    public function testUpdatesBundledProductsStockOnCancelIfEnvVariableIsSetToTrue(string $orderPaymentState): void
    {
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

    public function testUpdatesBundledProductsStockOnSellIfEnvVariableIsSetToTrue(): void
    {
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
}
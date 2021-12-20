<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Factory;

use BitBag\SyliusProductBundlePlugin\Factory\OrderItemFactory;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Tests\BitBag\SyliusProductBundlePlugin\Entity\OrderItem;

final class OrderItemFactoryTest extends TestCase
{
    public function testCreateOrderItemWithVariant(): void
    {
        $productVariant = new ProductVariant();
        $orderItem = new OrderItem();

        $baseFactory = $this->createMock(CartItemFactoryInterface::class);
        $baseFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($orderItem)
        ;

        $factory = new OrderItemFactory($baseFactory);
        $orderItemWithVariant = $factory->createWithVariant($productVariant);

        self::assertSame($productVariant, $orderItemWithVariant->getVariant());
    }
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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

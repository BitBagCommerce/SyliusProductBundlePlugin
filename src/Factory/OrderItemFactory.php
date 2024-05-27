<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Factory;

use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class OrderItemFactory implements OrderItemFactoryInterface
{
    public function __construct(
        private CartItemFactoryInterface $decoratedFactory,
    ) {
    }

    public function createNew(): OrderItemInterface
    {
        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->decoratedFactory->createNew();

        return $orderItem;
    }

    public function createWithVariant(ProductVariantInterface $productVariant): OrderItemInterface
    {
        $orderItem = $this->createNew();
        $orderItem->setVariant($productVariant);

        return $orderItem;
    }

    public function createForProduct(ProductInterface $product): \Sylius\Component\Core\Model\OrderItemInterface
    {
        return $this->decoratedFactory->createForProduct($product);
    }

    public function createForCart(OrderInterface $order): \Sylius\Component\Core\Model\OrderItemInterface
    {
        return $this->decoratedFactory->createForCart($order);
    }
}

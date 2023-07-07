<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
        private CartItemFactoryInterface $decoratedFactory
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

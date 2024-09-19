<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Inventory\Operator;

use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Core\Inventory\Exception\NotEnoughUnitsOnHandException;
use Sylius\Component\Core\Inventory\Exception\NotEnoughUnitsOnHoldException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductBundleOrderInventoryOperator implements ProductBundleOrderInventoryOperatorInterface
{
    public function hold(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            /** @var ProductInterface $product */
            $product = $orderItem->getProduct();
            if ($product->isBundle()) {
                $this->holdBundleOrderItem($orderItem);
            } else {
                $this->holdRegularOrderItem($orderItem);
            }
        }
    }

    public function sell(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            /** @var ProductInterface $product */
            $product = $orderItem->getProduct();
            if ($product->isBundle()) {
                $this->sellBundleOrderItem($orderItem);
            } else {
                $this->sellRegularOrderItem($orderItem);
            }
        }
    }

    /** @throws \InvalidArgumentException */
    public function release(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            /** @var ProductInterface $product */
            $product = $orderItem->getProduct();
            if ($product->isBundle()) {
                $this->releaseBundleOrderItem($orderItem);
            } else {
                $this->releaseRegularOrderItem($orderItem);
            }
        }
    }

    public function giveBack(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            /** @var ProductInterface $product */
            $product = $orderItem->getProduct();
            if ($product->isBundle()) {
                $this->giveBackBundleOrderItem($orderItem);
            } else {
                $this->giveBackRegularOrderItem($orderItem);
            }
        }
    }

    private function holdBundleOrderItem(OrderItemInterface $orderItem): void
    {
        foreach ($orderItem->getProductBundleOrderItems() as $bundleOrderItem) {
            $quantity = $orderItem->getQuantity() * (int) $bundleOrderItem->getQuantity();
            $variant = $bundleOrderItem->getProductVariant();

            $this->holdProductVariant($variant, $quantity);
        }
    }

    private function holdRegularOrderItem(OrderItemInterface $orderItem): void
    {
        $quantity = $orderItem->getQuantity();
        $variant = $orderItem->getVariant();

        $this->holdProductVariant($variant, $quantity);
    }

    private function holdProductVariant(?ProductVariantInterface $variant, int $quantity): void
    {
        if (null === $variant) {
            throw new \InvalidArgumentException('Variant cannot be null');
        }

        if (!$variant->isTracked()) {
            return;
        }

        $variant->setOnHold((int) $variant->getOnHold() + $quantity);
    }

    private function sellBundleOrderItem(OrderItemInterface $orderItem): void
    {
        foreach ($orderItem->getProductBundleOrderItems() as $bundleOrderItem) {
            $quantity = $orderItem->getQuantity() * (int) $bundleOrderItem->getQuantity();
            $variant = $bundleOrderItem->getProductVariant();

            $this->sellProductVariant($variant, $quantity);
        }
    }

    private function sellRegularOrderItem(OrderItemInterface $orderItem): void
    {
        $quantity = $orderItem->getQuantity();
        $variant = $orderItem->getVariant();

        $this->sellProductVariant($variant, $quantity);
    }

    private function sellProductVariant(?ProductVariantInterface $variant, int $quantity): void
    {
        if (null === $variant) {
            throw new \InvalidArgumentException('Variant cannot be null');
        }

        if (!$variant->isTracked()) {
            return;
        }

        if (((int) $variant->getOnHold() - $quantity) < 0) {
            throw new NotEnoughUnitsOnHoldException((string) $variant->getName());
        }

        if (((int) $variant->getOnHand() - $quantity) < 0) {
            throw new NotEnoughUnitsOnHandException((string) $variant->getName());
        }

        $variant->setOnHold((int) $variant->getOnHold() - $quantity);
        $variant->setOnHand((int) $variant->getOnHand() - $quantity);
    }

    private function releaseBundleOrderItem(OrderItemInterface $orderItem): void
    {
        foreach ($orderItem->getProductBundleOrderItems() as $bundleOrderItem) {
            $quantity = $orderItem->getQuantity() * (int) $bundleOrderItem->getQuantity();
            $variant = $bundleOrderItem->getProductVariant();

            $this->releaseProductVariant($variant, $quantity);
        }
    }

    private function releaseRegularOrderItem(OrderItemInterface $orderItem): void
    {
        $quantity = $orderItem->getQuantity();
        $variant = $orderItem->getVariant();

        $this->releaseProductVariant($variant, $quantity);
    }

    private function releaseProductVariant(?ProductVariantInterface $variant, int $quantity): void
    {
        if (null === $variant) {
            throw new \InvalidArgumentException('Variant cannot be null');
        }

        if (!$variant->isTracked()) {
            return;
        }

        if (((int) $variant->getOnHold() - $quantity) < 0) {
            throw new NotEnoughUnitsOnHoldException((string) $variant->getName());
        }

        $variant->setOnHold((int) $variant->getOnHold() - $quantity);
    }

    private function giveBackBundleOrderItem(OrderItemInterface $orderItem): void
    {
        foreach ($orderItem->getProductBundleOrderItems() as $bundleOrderItem) {
            $quantity = $orderItem->getQuantity() * (int) $bundleOrderItem->getQuantity();
            $variant = $bundleOrderItem->getProductVariant();

            $this->giveBackProductVariant($variant, $quantity);
        }
    }

    private function giveBackRegularOrderItem(OrderItemInterface $orderItem): void
    {
        $quantity = $orderItem->getQuantity();
        $variant = $orderItem->getVariant();

        $this->giveBackProductVariant($variant, $quantity);
    }

    private function giveBackProductVariant(?ProductVariantInterface $variant, int $quantity): void
    {
        if (null === $variant) {
            throw new \InvalidArgumentException('Variant cannot be null');
        }

        if (!$variant->isTracked()) {
            return;
        }

        $variant->setOnHand((int) $variant->getOnHand() + $quantity);
    }
}

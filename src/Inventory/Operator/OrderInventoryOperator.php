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
use BitBag\SyliusProductBundlePlugin\Inventory\Checker\FeatureFlagCheckerInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\OrderPaymentStates;

final class OrderInventoryOperator implements OrderInventoryOperatorInterface
{
    public function __construct(
        private readonly OrderInventoryOperatorInterface $decorated,
        private readonly EntityManagerInterface $productVariantManager,
        private readonly FeatureFlagCheckerInterface $featureFlagChecker,
        private readonly ProductBundleOrderInventoryOperatorInterface $productBundleOrderInventoryOperator,
    ) {
    }

    public function cancel(OrderInterface $order): void
    {
        $this->lockOrderProductVariants($order);

        if (!$this->featureFlagChecker->isEnabled()) {
            $this->decorated->cancel($order);
        }

        if (in_array(
            $order->getPaymentState(),
            [OrderPaymentStates::STATE_PAID, OrderPaymentStates::STATE_REFUNDED],
            true,
        )) {
            $this->productBundleOrderInventoryOperator->giveBack($order);

            return;
        }

        $this->productBundleOrderInventoryOperator->release($order);
    }

    public function hold(OrderInterface $order): void
    {
        $this->lockOrderProductVariants($order);

        if (!$this->featureFlagChecker->isEnabled()) {
            $this->decorated->hold($order);

            return;
        }

        $this->productBundleOrderInventoryOperator->hold($order);
    }

    public function sell(OrderInterface $order): void
    {
        $this->lockOrderProductVariants($order);

        if (!$this->featureFlagChecker->isEnabled()) {
            $this->decorated->sell($order);

            return;
        }

        $this->productBundleOrderInventoryOperator->sell($order);
    }

    private function lockOrderProductVariants(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $this->lockOrderItemProductVariants($orderItem);
        }
    }

    private function lockOrderItemProductVariants(OrderItemInterface $orderItem): void
    {
        /** @var ProductInterface $product */
        $product = $orderItem->getProduct();
        if ($this->featureFlagChecker->isEnabled() && $product->isBundle()) {
            $this->lockBundledOrderItemProductVariants($orderItem);
        } else {
            $this->lockOrderItemProductVariant($orderItem);
        }
    }

    private function lockOrderItemProductVariant(OrderItemInterface $orderItem): void
    {
        $this->lockProductVariant($orderItem->getVariant());
    }

    private function lockBundledOrderItemProductVariants(OrderItemInterface $orderItem): void
    {
        foreach ($orderItem->getProductBundleOrderItems() as $bundleOrderItem) {
            $this->lockProductVariant($bundleOrderItem->getProductVariant());
        }
    }

    private function lockProductVariant(?ProductVariantInterface $variant): void
    {
        if (null === $variant) {
            throw new \InvalidArgumentException('Variant cannot be null');
        }

        if (!$variant->isTracked()) {
            return;
        }

        $this->productVariantManager->lock($variant, LockMode::OPTIMISTIC, $variant->getVersion());
    }
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Inventory\Checker;

use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemInterface as BaseOrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductBundleOrderItemAvailabilityChecker implements ProductBundleOrderItemAvailabilityCheckerInterface
{
    public function areOrderedBundledProductVariantsAvailable(BaseOrderItemInterface|OrderItemInterface $orderItem): bool
    {
        if (!$orderItem instanceof OrderItemInterface) {
            return true;
        }

        foreach ($orderItem->getProductBundleOrderItems() as $bundleOrderItem) {
            $quantity = $orderItem->getQuantity() * (int) $bundleOrderItem->getQuantity();
            /** @var ProductVariantInterface $variant */
            $variant = $bundleOrderItem->getProductVariant();
            if (!$variant->isTracked()) {
                continue;
            }

            if (0 > (int) $variant->getOnHold() - $quantity || 0 > (int) $variant->getOnHand() - $quantity) {
                return false;
            }
        }

        return true;
    }
}

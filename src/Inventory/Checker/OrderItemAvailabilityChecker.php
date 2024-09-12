<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Inventory\Checker;

use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Core\Inventory\Checker\OrderItemAvailabilityCheckerInterface;
use Sylius\Component\Core\Model\OrderItemInterface as BaseOrderItemInterface;

final class OrderItemAvailabilityChecker implements OrderItemAvailabilityCheckerInterface
{
    public function __construct(
        private readonly OrderItemAvailabilityCheckerInterface $decorated,
        private readonly FeatureFlagCheckerInterface $featureFlagChecker,
        private readonly ProductBundleOrderItemAvailabilityCheckerInterface $bundleOrderItemAvailabilityChecker,
    ) {
    }

    public function isReservedStockSufficient(BaseOrderItemInterface $orderItem): bool
    {
        if (!$this->featureFlagChecker->isEnabled()) {
            return $this->decorated->isReservedStockSufficient($orderItem);
        }

        /** @var ProductInterface $product */
        $product = $orderItem->getProduct();
        if (!$product->isBundle()) {
            return $this->decorated->isReservedStockSufficient($orderItem);
        }

        return $this->bundleOrderItemAvailabilityChecker->areOrderedBundledProductVariantsAvailable($orderItem);
    }
}

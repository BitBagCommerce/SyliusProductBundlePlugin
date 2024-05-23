<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Doctrine\ORM\Inventory\Operator;

use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Webmozart\Assert\Assert;

class OrderInventoryOperator implements OrderInventoryOperatorInterface
{
    public function __construct(
        private OrderInventoryOperatorInterface $decorated,
        private bool $updateBundledProductsStock,
    ) {
    }

    public function cancel(OrderInterface $order): void
    {
        $this->decorated->cancel($order);

        if (true === $this->updateBundledProductsStock) {
            $productBundles = $this->getProductBundlesFromOrderItems($order->getItems());

            if (in_array(
                $order->getPaymentState(),
                [OrderPaymentStates::STATE_PAID, OrderPaymentStates::STATE_REFUNDED],
                true,
            )) {
                /** @var OrderItemInterface $productBundle */
                foreach ($productBundles as $productBundle) {
                    $this->updateBundledProductsStock($productBundle->getProductBundleOrderItems(), 'giveBack');
                }

                return;
            }

            /** @var OrderItemInterface $productBundle */
            foreach ($productBundles as $productBundle) {
                $this->updateBundledProductsStock($productBundle->getProductBundleOrderItems(), 'release');
            }
        }
    }

    public function hold(OrderInterface $order): void
    {
        $this->decorated->hold($order);

        if (true === $this->updateBundledProductsStock) {
            $productBundles = $this->getProductBundlesFromOrderItems($order->getItems());

            /** @var OrderItemInterface $productBundle */
            foreach ($productBundles as $productBundle) {
                $this->updateBundledProductsStock($productBundle->getProductBundleOrderItems(), 'hold');
            }
        }
    }

    public function sell(OrderInterface $order): void
    {
        $this->decorated->sell($order);

        if (true === $this->updateBundledProductsStock) {
            $productBundles = $this->getProductBundlesFromOrderItems($order->getItems());

            /** @var OrderItemInterface $productBundle */
            foreach ($productBundles as $productBundle) {
                $this->updateBundledProductsStock($productBundle->getProductBundleOrderItems(), 'sell');
            }
        }
    }

    protected function updateBundledProductsStock(Collection $bundledProducts, string $method): void
    {
        /** @var ProductBundleOrderItemInterface $bundledProduct */
        foreach ($bundledProducts as $bundledProduct) {
            $variant = $bundledProduct->getProductVariant();
            if (!$variant->isTracked()) {
                continue;
            }

            switch ($method) {
                case 'giveBack':
                    $variant->setOnHand($variant->getOnHand() + $bundledProduct->getQuantity());
                    break;
                case 'release':
                    Assert::greaterThanEq(
                        ($variant->getOnHold() - $bundledProduct->getQuantity()),
                        0,
                        sprintf(
                            'Not enough units to decrease on hold quantity from the inventory of a variant "%s".',
                            $variant->getName(),
                        ),
                    );

                    $variant->setOnHold($variant->getOnHold() - $bundledProduct->getQuantity());
                    break;
                case 'hold':
                    $variant->setOnHold($variant->getOnHold() + $bundledProduct->getQuantity());
                    break;
                case 'sell':
                    Assert::greaterThanEq(
                        ($variant->getOnHold() - $bundledProduct->getQuantity()),
                        0,
                        sprintf(
                            'Not enough units to decrease on hold quantity from the inventory of a variant "%s".',
                            $variant->getName(),
                        ),
                    );

                    Assert::greaterThanEq(
                        ($variant->getOnHand() - $bundledProduct->getQuantity()),
                        0,
                        sprintf(
                            'Not enough units to decrease on hand quantity from the inventory of a variant "%s".',
                            $variant->getName(),
                        ),
                    );

                    $variant->setOnHold($variant->getOnHold() - $bundledProduct->getQuantity());
                    $variant->setOnHand($variant->getOnHand() - $bundledProduct->getQuantity());
                    break;
            }
        }
    }

    public function getProductBundlesFromOrderItems(Collection $orderItems): Collection
    {
        return $orderItems->filter(
            fn(OrderItemInterface $orderItem) => 0 !== $orderItem->getProductBundleOrderItems()->count()
        );
    }
}
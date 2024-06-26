<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Factory\OrderItemFactoryInterface;
use BitBag\SyliusProductBundlePlugin\Factory\ProductBundleOrderItemFactoryInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Webmozart\Assert\Assert;

final class CartProcessor implements CartProcessorInterface
{
    public function __construct(
        private OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        private ProductBundleOrderItemFactoryInterface $productBundleOrderItemFactory,
        private OrderModifierInterface $orderModifier,
        private OrderItemFactoryInterface $cartItemFactory,
    ) {
    }

    public function process(
        OrderInterface $cart,
        ProductBundleInterface $productBundle,
        int $quantity,
    ): void {
        Assert::greaterThan($quantity, 0);

        $product = $productBundle->getProduct();
        Assert::notNull($product);

        /** @var ProductVariantInterface|false $productVariant */
        $productVariant = $product->getVariants()->first();
        Assert::notFalse($productVariant);

        $cartItem = $this->cartItemFactory->createWithVariant($productVariant);
        $this->orderItemQuantityModifier->modify($cartItem, $quantity);

        foreach ($productBundle->getProductBundleItems() as $bundleItem) {
            $productBundleOrderItem = $this->productBundleOrderItemFactory->createFromProductBundleItem($bundleItem);
            $cartItem->addProductBundleOrderItem($productBundleOrderItem);
        }

        $this->orderModifier->addToOrder($cart, $cartItem);
    }
}

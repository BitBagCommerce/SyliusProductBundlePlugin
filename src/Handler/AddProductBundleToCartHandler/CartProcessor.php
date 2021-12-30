<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    /** @var ProductBundleOrderItemFactoryInterface */
    private $productBundleOrderItemFactory;

    /** @var OrderModifierInterface */
    private $orderModifier;

    /** @var OrderItemFactoryInterface */
    private $cartItemFactory;

    public function __construct(
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        ProductBundleOrderItemFactoryInterface $productBundleOrderItemFactory,
        OrderModifierInterface $orderModifier,
        OrderItemFactoryInterface $cartItemFactory
    ) {
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->productBundleOrderItemFactory = $productBundleOrderItemFactory;
        $this->orderModifier = $orderModifier;
        $this->cartItemFactory = $cartItemFactory;
    }

    public function process(
        OrderInterface $cart,
        ProductBundleInterface $productBundle,
        int $quantity
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

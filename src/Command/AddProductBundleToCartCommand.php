<?php

namespace BitBag\SyliusProductBundlePlugin\Command;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class AddProductBundleToCartCommand
{
    /** @var OrderInterface */
    private $cart;

    /** @var OrderItemInterface */
    private $cartItem;

    /** @var ProductInterface */
    private $product;

    /** @var AddProductBundleItemToCartCommand[] */
    private $productBundleItems = [];

    public function __construct(OrderInterface $cart, OrderItemInterface $cartItem, ProductInterface $product)
    {
        $this->cart = $cart;
        $this->cartItem = $cartItem;
        $this->product = $product;

        /** @var ProductBundleItemInterface $productBundleItem */
        foreach ($product->getProductBundle()->getProductBundleItems() as $productBundleItem) {
            $this->productBundleItems[] = new AddProductBundleItemToCartCommand(
                $productBundleItem->getProductVariant(),
                $productBundleItem->getQuantity()
            );
        }
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getProductBundleItems(): array
    {
        return $this->productBundleItems;
    }

    public function getCart(): OrderInterface
    {
        return $this->cart;
    }

    public function getCartItem(): OrderItemInterface
    {
        return $this->cartItem;
    }
}

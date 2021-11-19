<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Command;

use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Order\Model\OrderInterface;

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

    public function __construct(
        OrderInterface $cart,
        OrderItemInterface $cartItem,
        ProductInterface $product
    ) {
        $this->cart = $cart;
        $this->cartItem = $cartItem;
        $this->product = $product;
        assert(null !== $product->getProductBundle());
        /** @var ProductBundleItemInterface $productBundleItem */
        foreach ($product->getProductBundle()->getProductBundleItems() as $productBundleItem) {
            $this->productBundleItems[] = new AddProductBundleItemToCartCommand($productBundleItem);
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

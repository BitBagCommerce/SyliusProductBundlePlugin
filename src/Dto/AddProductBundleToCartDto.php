<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Dto;

use BitBag\SyliusProductBundlePlugin\Command\ProductCodeAwareInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

final class AddProductBundleToCartDto implements AddProductBundleToCartDtoInterface, ProductCodeAwareInterface
{
    private ArrayCollection $productBundleItems;

    public function __construct(
        private OrderInterface $cart,
        private OrderItemInterface $cartItem,
        private ProductInterface $product,
        array $productBundleItems
    ) {
        $this->productBundleItems = new ArrayCollection($productBundleItems);
    }

    public function getCart(): OrderInterface
    {
        return $this->cart;
    }

    public function setCart(OrderInterface $cart): void
    {
        $this->cart = $cart;
    }

    public function getCartItem(): OrderItemInterface
    {
        return $this->cartItem;
    }

    public function setCartItem(OrderItemInterface $cartItem): void
    {
        $this->cartItem = $cartItem;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }

    public function getProductBundleItems(): ArrayCollection
    {
        return $this->productBundleItems;
    }

    public function getProductCode(): string
    {
        return $this->product->getCode() ?? '';
    }
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
        array $productBundleItems,
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

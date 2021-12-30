<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Dto;

use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

interface AddProductBundleToCartDtoInterface
{
    public function getCart(): OrderInterface;

    public function setCart(OrderInterface $cart): void;

    public function getCartItem(): OrderItemInterface;

    public function setCartItem(OrderItemInterface $cartItem): void;

    public function getProduct(): ProductInterface;

    public function setProduct(ProductInterface $product): void;

    public function getProductBundleItems(): ArrayCollection;
}

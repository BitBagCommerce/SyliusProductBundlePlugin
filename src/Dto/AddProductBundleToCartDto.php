<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Dto;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommand;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;

final class AddProductBundleToCartDto
{
    /** @var OrderInterface */
    private $cart;
    /** @var OrderItemInterface */
    private $cartItem;
    /** @var ProductInterface */
    private $product;
    /** @var ArrayCollection */
    private $productBundleItems;

    public function __construct(OrderInterface $cart, OrderItemInterface $cartItem, ProductInterface $product)
    {
        $this->cart = $cart;
        $this->cartItem = $cartItem;
        $this->product = $product;
        $this->productBundleItems = new ArrayCollection();

        foreach ($product->getProductBundle()->getProductBundleItems() as $productBundleItem) {
            $this->productBundleItems->add(new AddProductBundleItemToCartCommand($productBundleItem));
        }
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

    /**
     * @return ArrayCollection
     */
    public function getProductBundleItems(): ArrayCollection
    {
        return $this->productBundleItems;
    }
}

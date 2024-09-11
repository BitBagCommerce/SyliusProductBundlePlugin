<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Handler\AddProductBundleToCartHandler;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommandInterface;
use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundle;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItem;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\Factory\OrderItemFactoryInterface;
use BitBag\SyliusProductBundlePlugin\Factory\ProductBundleOrderItemFactoryInterface;
use BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler\CartProcessor;
use BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler\CartProcessorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Order\Model\Order;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Tests\BitBag\SyliusProductBundlePlugin\Entity\OrderItem;
use Tests\BitBag\SyliusProductBundlePlugin\Entity\Product;
use Webmozart\Assert\InvalidArgumentException;

final class CartProcessorTest extends TestCase
{
    private OrderItemQuantityModifierInterface|MockObject $orderItemQuantityModifier;

    private ProductBundleOrderItemFactoryInterface|MockObject $productBundleOrderItemFactory;

    private OrderModifierInterface|MockObject $orderModifier;

    private OrderItemFactoryInterface|MockObject $cartItemFactory;

    private AddProductBundleItemToCartCommandInterface|MockObject $addProductBundleItemToCartCommand;

    protected function setUp(): void
    {
        $this->orderItemQuantityModifier = $this->createMock(OrderItemQuantityModifierInterface::class);
        $this->productBundleOrderItemFactory = $this->createMock(ProductBundleOrderItemFactoryInterface::class);
        $this->orderModifier = $this->createMock(OrderModifierInterface::class);
        $this->cartItemFactory = $this->createMock(OrderItemFactoryInterface::class);
        $this->addProductBundleItemToCartCommand = $this->createMock(AddProductBundleItemToCartCommandInterface::class);
    }

    public function testThrowExceptionIfQuantityNotGreaterThanZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $cart = $this->createCart();
        $productBundle = $this->createProductBundle();

        $processor = $this->createProcessor();
        $processor->process($cart, $productBundle, 0, new ArrayCollection());
    }

    public function testThrowExceptionIfProductIsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $cart = $this->createCart();
        $productBundle = $this->createProductBundle();

        $processor = $this->createProcessor();
        $processor->process($cart, $productBundle, 1, new ArrayCollection());
    }

    public function testThrowExceptionIfProductHasNoVariant(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $cart = $this->createCart();
        $productBundle = $this->createProductBundleWithProduct();

        $processor = $this->createProcessor();
        $processor->process($cart, $productBundle, 1, new ArrayCollection());
    }

    public function testCreateCartItem(): void
    {
        $cart = $this->createCart();
        $productVariant = new ProductVariant();
        $product = $this->createProductWithVariant($productVariant);
        $productBundle = $this->createProductBundleWithProduct($product);

        $this->cartItemFactory->expects(self::once())
            ->method('createWithVariant')
            ->with($productVariant)
        ;

        $processor = $this->createProcessor();
        $processor->process($cart, $productBundle, 2, new ArrayCollection([$this->addProductBundleItemToCartCommand]));
    }

    public function testModifyCartItemQuantity(): void
    {
        $cart = $this->createCart();
        $productVariant = new ProductVariant();
        $product = $this->createProductWithVariant($productVariant);
        $productBundle = $this->createProductBundleWithProduct($product);
        $cartItem = $this->createCartItem();

        $this->cartItemFactory
            ->method('createWithVariant')
            ->willReturn($cartItem)
        ;
        $this->orderItemQuantityModifier->expects(self::once())
            ->method('modify')
            ->with($cartItem, 2)
        ;

        $processor = $this->createProcessor();
        $processor->process($cart, $productBundle, 2, new ArrayCollection([$this->addProductBundleItemToCartCommand]));
    }

    public function testCreateBundleOrderItemsFromBundleItems(): void
    {
        $addBundleItemToCartCommand1 = $this->createMock(AddProductBundleItemToCartCommandInterface::class);
        $addBundleItemToCartCommand2 = $this->createMock(AddProductBundleItemToCartCommandInterface::class);

        $productBundleOrderItem1 = $this->createProductBundleOrderItem();
        $productBundleOrderItem2 = $this->createProductBundleOrderItem();

        $cart = $this->createCart();
        $product = $this->createProductWithVariant();
        $productBundle = $this->createProductBundleWithProduct($product);

        $cartItem = $this->createMock(OrderItemInterface::class);
        $cartItem->expects(self::exactly(2))
            ->method('addProductBundleOrderItem')
            ->withConsecutive([$productBundleOrderItem1], [$productBundleOrderItem2])
        ;

        $this->cartItemFactory
            ->method('createWithVariant')
            ->willReturn($cartItem)
        ;
        $this->productBundleOrderItemFactory->expects(self::exactly(2))
            ->method('createFromAddProductBundleItemToCartCommand')
            ->withConsecutive([$addBundleItemToCartCommand1], [$addBundleItemToCartCommand2])
            ->willReturn($productBundleOrderItem1, $productBundleOrderItem2)
        ;

        $processor = $this->createProcessor();
        $processor->process($cart, $productBundle, 1, new ArrayCollection([$addBundleItemToCartCommand1, $addBundleItemToCartCommand2]));
    }

    public function testAddCartItemToOrder(): void
    {
        $cart = $this->createCart();
        $product = $this->createProductWithVariant();
        $productBundle = $this->createProductBundleWithProduct($product);
        $cartItem = $this->createCartItem();

        $this->cartItemFactory
            ->method('createWithVariant')
            ->willReturn($cartItem)
        ;
        $this->orderModifier->expects(self::once())
            ->method('addToOrder')
            ->with($cart, $cartItem)
        ;

        $processor = $this->createProcessor();
        $processor->process($cart, $productBundle, 1, new ArrayCollection([$this->addProductBundleItemToCartCommand]));
    }

    private function createProcessor(): CartProcessorInterface
    {
        return new CartProcessor(
            $this->orderItemQuantityModifier,
            $this->productBundleOrderItemFactory,
            $this->orderModifier,
            $this->cartItemFactory,
        );
    }

    private function createCart(): OrderInterface
    {
        return new Order();
    }

    private function createCartItem(): OrderItemInterface
    {
        return new OrderItem();
    }

    private function createProductWithVariant(?ProductVariantInterface $productVariant = null): ProductInterface
    {
        if (null === $productVariant) {
            $productVariant = new ProductVariant();
        }

        $product = new Product();
        $product->addVariant($productVariant);

        return $product;
    }

    private function createProductBundle(): ProductBundleInterface
    {
        return new ProductBundle();
    }

    private function createProductBundleWithProduct(?ProductInterface $product = null): ProductBundleInterface
    {
        if (null === $product) {
            $product = new Product();
        }

        $productBundle = $this->createProductBundle();
        $productBundle->setProduct($product);

        return $productBundle;
    }

    private function createProductBundleOrderItem(): ProductBundleOrderItemInterface
    {
        return new ProductBundleOrderItem();
    }
}

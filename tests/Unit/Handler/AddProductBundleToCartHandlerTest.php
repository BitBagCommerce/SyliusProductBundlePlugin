<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Handler;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundle;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItem;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItem;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Factory\ProductBundleOrderItemFactoryInterface;
use BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Tests\BitBag\SyliusProductBundlePlugin\Entity\OrderItem;
use Webmozart\Assert\InvalidArgumentException;

final class AddProductBundleToCartHandlerTest extends TestCase
{
    /** @var mixed|MockObject|OrderRepositoryInterface  */
    private $orderRepository;
    /** @var mixed|MockObject|RepositoryInterface  */
    private $productBundleRepository;
    /** @var mixed|MockObject|FactoryInterface  */
    private $orderItemFactory;
    /** @var ProductBundleOrderItemFactoryInterface|mixed|MockObject  */
    private $productBundleOrderItemFactory;
    /** @var mixed|MockObject|OrderModifierInterface  */
    private $orderModifier;
    /** @var mixed|MockObject|OrderItemQuantityModifierInterface  */
    private $orderItemQuantityModifier;
    /** @var EntityManagerInterface|mixed|MockObject  */
    private $orderManager;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->productBundleRepository = $this->createMock(RepositoryInterface::class);
        $this->orderItemFactory = $this->createMock(FactoryInterface::class);
        $this->productBundleOrderItemFactory = $this->createMock(ProductBundleOrderItemFactoryInterface::class);
        $this->orderModifier = $this->createMock(OrderModifierInterface::class);
        $this->orderItemQuantityModifier = $this->createMock(OrderItemQuantityModifierInterface::class);
        $this->orderManager = $this->createMock(EntityManagerInterface::class);
    }

    /** @test */
    public function it_should_throw_exception_if_cart_not_found(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $command = new AddProductBundleToCartCommand();
        $command->setOrderId(1);

        $this->orderRepository
            ->expects($this->once())
            ->method('findCartById')
            ->willReturn(null)
        ;

        $handler = $this->createHandler();
        $handler($command);
    }

    /** @test */
    public function it_should_throw_exception_if_product_bundle_not_found(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $command = new AddProductBundleToCartCommand();
        $command->setOrderId(1);

        $this->makeOrderRepositoryStagePassable();

        $this->productBundleRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn(null)
        ;

        $handler = $this->createHandler();
        $handler($command);
    }

    /** @test */
    public function it_should_throw_exception_if_product_bundle_doesnt_have_set_product(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $command = new AddProductBundleToCartCommand();
        $command->setOrderId(1);

        $this->makeOrderRepositoryStagePassable();
        $this->makeProductBundleRepositoryStagePassable(new ProductBundle());

        $handler = $this->createHandler();
        $handler($command);
    }

    /** @test */
    public function it_should_throw_exception_if_product_has_no_variant(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $command = new AddProductBundleToCartCommand();
        $command->setOrderId(1);

        $this->makeOrderRepositoryStagePassable();

        $productBundle = new ProductBundle();
        $productBundle->setProduct(new Product());

        $this->makeProductBundleRepositoryStagePassable($productBundle);

        $handler = $this->createHandler();
        $handler($command);
    }

    /** @test */
    public function it_should_create_new_order_item_and_set_variant_from_command(): void
    {
        $command = new AddProductBundleToCartCommand();
        $command->setOrderId(1);

        $this->makeOrderRepositoryStagePassable();

        $productBundle = $this->createBasicProductBundle();
        $productVariant = $productBundle->getProduct()->getVariants()->first();

        $this->makeProductBundleRepositoryStagePassable($productBundle);

        $cartItem = $this->createMock(OrderItemInterface::class);
        $cartItem->expects($this->once())
            ->method('setVariant')
            ->with($productVariant)
        ;

        $this->orderItemFactory->expects($this->once())
            ->method('createNew')
            ->willReturn($cartItem)
        ;

        $handler = $this->createHandler();
        $handler($command);
    }

    /** @test */
    public function it_should_assign_quantity_from_command(): void
    {
        $command = new AddProductBundleToCartCommand(5);
        $command->setOrderId(1);

        $this->makeOrderRepositoryStagePassable();

        $productBundle = $this->createBasicProductBundle();

        $this->makeProductBundleRepositoryStagePassable($productBundle);

        $cartItem = new OrderItem();
        $this->orderItemFactory->expects($this->once())
            ->method('createNew')
            ->willReturn($cartItem)
        ;
        $this->orderItemQuantityModifier->expects($this->once())
            ->method('modify')
            ->with($cartItem, 5)
        ;

        $handler = $this->createHandler();
        $handler($command);
    }

    /** @test */
    public function it_should_add_product_bundle_order_items_to_cart(): void
    {
        $command = new AddProductBundleToCartCommand(5);
        $command->setOrderId(1);

        $this->makeOrderRepositoryStagePassable();

        $productVariant1 = new ProductVariant();
        $productVariant2 = new ProductVariant();

        $bundleItem1 = $this->createProductBundleItem($productVariant1, 1);
        $bundleItem2 = $this->createProductBundleItem($productVariant2, 2);
        $productBundle = $this->createBasicProductBundle();
        $productBundle->addProductBundleItem($bundleItem1);
        $productBundle->addProductBundleItem($bundleItem2);

        $this->makeProductBundleRepositoryStagePassable($productBundle);

        $bundleOrderItem1 = $this->createProductBundleOrderItemFromProductBundleItem($bundleItem1);
        $bundleOrderItem2 = $this->createProductBundleOrderItemFromProductBundleItem($bundleItem2);

        $cart = $this->createMock(OrderItemInterface::class);
        $cart->expects($this->exactly(2))
            ->method('addProductBundleOrderItem')
            ->withConsecutive([$bundleOrderItem1], [$bundleOrderItem2])
        ;

        $this->orderItemFactory->expects($this->once())
            ->method('createNew')
            ->willReturn($cart)
        ;

        $this->productBundleOrderItemFactory->expects($this->exactly(2))
            ->method('createFromProductBundleItem')
            ->withConsecutive([$bundleItem1], [$bundleItem2])
            ->willReturnOnConsecutiveCalls($bundleOrderItem1, $bundleOrderItem2)
        ;

        $handler = $this->createHandler();
        $handler($command);
    }

    /** @test */
    public function it_should_add_cart_item_to_cart_and_persist_it(): void
    {
        $command = new AddProductBundleToCartCommand(5);
        $command->setOrderId(1);

        $cart = new Order();
        $this->makeOrderRepositoryStagePassable($cart);

        $bundle = $this->createBasicProductBundle();
        $this->makeProductBundleRepositoryStagePassable($bundle);

        $cartItem = new OrderItem();
        $this->orderItemFactory->expects($this->once())
            ->method('createNew')
            ->willReturn($cartItem)
        ;

        $this->orderModifier->expects($this->once())
            ->method('addToOrder')
            ->with($cart, $cartItem)
        ;
        $this->orderManager->expects($this->once())
            ->method('persist')
            ->with($cart)
        ;
        $this->orderManager->expects($this->once())
            ->method('flush')
        ;

        $handler = $this->createHandler();
        $handler($command);
    }

    private function createHandler(): AddProductBundleToCartHandler
    {
        return new AddProductBundleToCartHandler(
            $this->orderRepository,
            $this->productBundleRepository,
            $this->orderItemFactory,
            $this->productBundleOrderItemFactory,
            $this->orderModifier,
            $this->orderItemQuantityModifier,
            $this->orderManager
        );
    }

    private function makeOrderRepositoryStagePassable(?OrderInterface $order = null): void
    {
        if (null === $order) {
            $order = new Order();
        }

        $this->orderRepository
            ->expects($this->once())
            ->method('findCartById')
            ->willReturn($order)
        ;
    }

    private function makeProductBundleRepositoryStagePassable(?ProductBundleInterface $productBundle = null): void
    {
        if (null === $productBundle) {
            $productBundle = new ProductBundle();
        }

        $this->productBundleRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn($productBundle)
        ;
    }

    private function createBasicProductBundle(): ProductBundleInterface
    {
        $productVariant = new ProductVariant();

        $product = new Product();
        $product->addVariant($productVariant);

        $productBundle = new ProductBundle();
        $productBundle->setProduct($product);

        return $productBundle;
    }

    private function createProductBundleItem(
        ProductVariantInterface $productVariant,
        int $quantity
    ): ProductBundleItemInterface {
        $productBundleItem = new ProductBundleItem();
        $productBundleItem->setQuantity($quantity);
        $productBundleItem->setProductVariant($productVariant);

        return $productBundleItem;
    }

    private function createProductBundleOrderItemFromProductBundleItem(ProductBundleItemInterface $bundleItem): ProductBundleOrderItemInterface
    {
        $orderItem = new ProductBundleOrderItem();
        $orderItem->setProductBundleItem($bundleItem);
        $orderItem->setQuantity($bundleItem->getQuantity());
        $orderItem->setProductVariant($bundleItem->getProductVariant());

        return $orderItem;
    }
}

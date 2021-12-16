<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Handler;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundle;
use BitBag\SyliusProductBundlePlugin\Factory\ProductBundleOrderItemFactoryInterface;
use BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
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

        $this->orderRepository
            ->expects($this->once())
            ->method('findCartById')
            ->willReturn(new Order())
        ;

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

        $this->orderRepository
            ->expects($this->once())
            ->method('findCartById')
            ->willReturn(new Order())
        ;

        $this->productBundleRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn(new ProductBundle())
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
}

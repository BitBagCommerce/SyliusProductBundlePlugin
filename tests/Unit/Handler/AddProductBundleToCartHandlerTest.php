<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Handler;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler;
use BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler\CartProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\OrderMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductBundleMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductMother;
use Webmozart\Assert\InvalidArgumentException;

final class AddProductBundleToCartHandlerTest extends TestCase
{
    /** @var mixed|MockObject|OrderRepositoryInterface */
    private $orderRepository;

    /** @var mixed|MockObject|ProductRepositoryInterface */
    private $productRepository;

    /** @var CartProcessorInterface|mixed|MockObject */
    private $cartProcessor;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->cartProcessor = $this->createMock(CartProcessorInterface::class);
    }

    public function testThrowExceptionIfCartDoesntExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value other than null.');

        $this->orderRepository->expects(self::once())
            ->method('findCartById')
            ->willReturn(null)
        ;

        $command = new AddProductBundleToCartCommand(0, '', 1);
        $handler = $this->createHandler();
        $handler($command);
    }

    public function testThrowExceptionIfProductDoesntExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value other than null.');

        $cart = OrderMother::create();
        $this->orderRepository->method('findCartById')
            ->willReturn($cart)
        ;
        $this->productRepository->expects(self::once())
            ->method('findOneByCode')
            ->willReturn(null)
        ;

        $command = new AddProductBundleToCartCommand(0, '', 1);
        $handler = $this->createHandler();
        $handler($command);
    }

    public function testThrowExceptionIfProductIsNotBundle(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value to be true. Got: false');

        $cart = OrderMother::create();
        $this->orderRepository->method('findCartById')
            ->willReturn($cart)
        ;

        $product = ProductMother::create();
        $this->productRepository->method('findOneByCode')
            ->willReturn($product)
        ;

        $command = new AddProductBundleToCartCommand(0, '', 1);
        $handler = $this->createHandler();
        $handler($command);
    }

    public function testThrowExceptionIfQuantityNotGreaterThanZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value greater than 0. Got: 0');

        $cart = OrderMother::create();
        $this->orderRepository->method('findCartById')
            ->willReturn($cart)
        ;

        $productBundle = ProductBundleMother::create();
        $product = ProductMother::createWithBundle($productBundle);
        $this->productRepository->method('findOneByCode')
            ->willReturn($product)
        ;

        $command = new AddProductBundleToCartCommand(0, '', 0);
        $handler = $this->createHandler();
        $handler($command);
    }

    public function testProcessCart(): void
    {
        $cart = OrderMother::create();
        $this->orderRepository->method('findCartById')
            ->willReturn($cart)
        ;

        $productBundle = ProductBundleMother::create();
        $product = ProductMother::createWithBundle($productBundle);
        $this->productRepository->method('findOneByCode')
            ->willReturn($product)
        ;

        $this->cartProcessor->expects(self::once())
            ->method('process')
            ->with($cart, $productBundle, 2)
        ;

        $command = new AddProductBundleToCartCommand(1, '', 2);
        $handler = $this->createHandler();
        $handler($command);
    }

    public function testAddCartToRepository(): void
    {
        $cart = OrderMother::create();
        $this->orderRepository->method('findCartById')
            ->willReturn($cart)
        ;

        $productBundle = ProductBundleMother::create();
        $product = ProductMother::createWithBundle($productBundle);
        $this->productRepository->method('findOneByCode')
            ->willReturn($product)
        ;

        $this->orderRepository->expects(self::once())
            ->method('add')
            ->with($cart)
        ;

        $command = new AddProductBundleToCartCommand(1, '', 1);
        $handler = $this->createHandler();
        $handler($command);
    }

    private function createHandler(): AddProductBundleToCartHandler
    {
        return new AddProductBundleToCartHandler(
            $this->orderRepository,
            $this->productRepository,
            $this->cartProcessor
        );
    }
}

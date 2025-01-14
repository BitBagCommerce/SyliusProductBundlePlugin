<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Handler\Api;

use BitBag\SyliusProductBundlePlugin\Dto\Api\AddProductBundleToCartDto;
use BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler\CartProcessorInterface;
use BitBag\SyliusProductBundlePlugin\Handler\Api\AddProductBundleToCartHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\OrderMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductBundleMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\TypeExceptionMessage;
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
        $this->expectExceptionMessage(TypeExceptionMessage::EXPECTED_VALUE_OTHER_THAN_NULL);

        $this->orderRepository
            ->method('findCartByTokenValue')
            ->willReturn(null)
        ;

        $command = new AddProductBundleToCartDto('', '', 1);
        $handler = $this->createHandler();
        $handler($command);
    }

    public function testProcessCart(): void
    {
        $cart = OrderMother::create();
        $this->orderRepository
            ->method('findCartByTokenValue')
            ->willReturn($cart)
        ;

        $productBundle = ProductBundleMother::create();
        $product = ProductMother::createWithBundle($productBundle);
        $this->productRepository
            ->method('findOneByCode')
            ->willReturn($product)
        ;

        $this->cartProcessor->expects(self::once())
            ->method('process')
            ->with($cart, $productBundle, 2)
        ;

        $command = new AddProductBundleToCartDto('', '', 2);
        $handler = $this->createHandler();
        $handler($command);
    }

    private function createHandler(): AddProductBundleToCartHandler
    {
        return new AddProductBundleToCartHandler(
            $this->orderRepository,
            $this->productRepository,
            $this->cartProcessor,
        );
    }
}

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
use BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler;
use BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler\CartProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\InvalidArgumentException;

final class AddProductBundleToCartHandlerTest extends TestCase
{
    /** @var mixed|MockObject|OrderRepositoryInterface */
    private $orderRepository;

    /** @var mixed|MockObject|RepositoryInterface */
    private $productBundleRepository;

    /** @var CartProcessorInterface|mixed|MockObject */
    private $cartProcessor;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->productBundleRepository = $this->createMock(RepositoryInterface::class);
        $this->cartProcessor = $this->createMock(CartProcessorInterface::class);
    }

    public function testThrowExceptionIfCartNotFound(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findCartById')
            ->willReturn(null)
        ;

        $command = new AddProductBundleToCartCommand();
        $handler = $this->createHandler();
        $handler($command);
    }

    public function testThrowExceptionIfProductBundleNotFound(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->makeOrderRepositoryStagePassable();

        $this->productBundleRepository
            ->expects(self::once())
            ->method('find')
            ->willReturn(null)
        ;

        $command = new AddProductBundleToCartCommand();
        $handler = $this->createHandler();
        $handler($command);
    }

    public function testThrowExceptionIfQuantityNotGreaterThanZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->makeOrderRepositoryStagePassable();
        $this->makeProductBundleRepositoryStagePassable();

        $command = new AddProductBundleToCartCommand(-1);
        $handler = $this->createHandler();
        $handler($command);
    }

    public function testProcessCart(): void
    {
        $this->cartProcessor->expects(self::once())
            ->method('process')
        ;

        $this->makeOrderRepositoryStagePassable();
        $this->makeProductBundleRepositoryStagePassable();

        $command = new AddProductBundleToCartCommand();
        $handler = $this->createHandler();
        $handler($command);
    }

    public function testAddCartToRepository(): void
    {
        $order = new Order();
        $this->makeOrderRepositoryStagePassable($order);
        $this->makeProductBundleRepositoryStagePassable();

        $this->orderRepository->expects(self::once())
            ->method('add')
            ->with($order)
        ;

        $command = new AddProductBundleToCartCommand();
        $handler = $this->createHandler();
        $handler($command);
    }

    private function createHandler(): AddProductBundleToCartHandler
    {
        return new AddProductBundleToCartHandler(
            $this->orderRepository,
            $this->productBundleRepository,
            $this->cartProcessor
        );
    }

    private function makeOrderRepositoryStagePassable(?OrderInterface $order = null): void
    {
        if (null === $order) {
            $order = new Order();
        }

        $this->orderRepository
            ->expects(self::once())
            ->method('findCartById')
            ->willReturn($order)
        ;
    }

    private function makeProductBundleRepositoryStagePassable(): void
    {
        $this->productBundleRepository
            ->expects(self::once())
            ->method('find')
            ->willReturn(new ProductBundle())
        ;
    }
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\EventListener;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\EventListener\AddProductToProductBundleWhenEditNormalProductEventListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

final class AddProductToProductBundleWhenEditNormalProductEventListenerTest extends TestCase
{
    private AddProductToProductBundleWhenEditNormalProductEventListener $instance;

    private ResourceControllerEvent|MockObject $resourceControllerEvent;

    private ProductInterface|MockObject $product;

    private ProductBundleInterface|MockObject $productBundle;

    protected function setUp(): void
    {
        $this->instance = new AddProductToProductBundleWhenEditNormalProductEventListener();
        $this->resourceControllerEvent = $this->createMock(ResourceControllerEvent::class);
        $this->product = $this->createMock(ProductInterface::class);
        $this->productBundle = $this->createMock(ProductBundleInterface::class);
    }

    public function testAddProductToProductBundle(): void
    {
        $this->productBundle
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn(null);

        $this->product
            ->expects(self::exactly(3))
            ->method('getProductBundle')
            ->willReturn($this->productBundle);

        $this->productBundle
            ->expects(self::once())
            ->method('setProduct')
            ->with($this->product);

        $this->resourceControllerEvent
            ->expects(self::once())
            ->method('getSubject')
            ->willReturn($this->product);

        $this->instance->addProductToProductBundle($this->resourceControllerEvent);
    }

    public function testWillNotAddProductToProductBundleIfProductHasBundle(): void
    {
        $this->productBundle
            ->expects(self::never())
            ->method('getProduct');

        $this->product
            ->expects(self::once())
            ->method('getProductBundle')
            ->willReturn(null);

        $this->productBundle
            ->expects(self::never())
            ->method('setProduct');

        $this->resourceControllerEvent
            ->expects(self::once())
            ->method('getSubject')
            ->willReturn($this->product);

        $this->instance->addProductToProductBundle($this->resourceControllerEvent);
    }

    public function testWillNotAddProductToProductBundleIfProductBundleHasProduct(): void
    {
        $this->productBundle
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($this->product);

        $this->product
            ->expects(self::exactly(2))
            ->method('getProductBundle')
            ->willReturn($this->productBundle);

        $this->productBundle
            ->expects(self::never())
            ->method('setProduct');

        $this->resourceControllerEvent
            ->expects(self::once())
            ->method('getSubject')
            ->willReturn($this->product);

        $this->instance->addProductToProductBundle($this->resourceControllerEvent);
    }
}

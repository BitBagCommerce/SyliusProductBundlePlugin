<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Factory;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItem;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItem;
use BitBag\SyliusProductBundlePlugin\Factory\ProductBundleOrderItemFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductBundleOrderItemFactoryTest extends TestCase
{
    private const PRODUCT_VARIANT_CODE = 'MY_VARIANT';

    /** @var mixed|MockObject|FactoryInterface */
    private $baseProductBundleOrderItemFactory;

    protected function setUp(): void
    {
        $this->baseProductBundleOrderItemFactory = $this->createMock(FactoryInterface::class);
        $this->baseProductBundleOrderItemFactory
            ->method('createNew')
            ->willReturn(new ProductBundleOrderItem())
        ;
    }

    public function testCreateProductBundleOrderItemFromProductBundleItem(): void
    {
        $factory = new ProductBundleOrderItemFactory($this->baseProductBundleOrderItemFactory);

        $productVariant = new ProductVariant();
        $productVariant->setCode(self::PRODUCT_VARIANT_CODE);

        $productBundleItem = new ProductBundleItem();
        $productBundleItem->setProductVariant($productVariant);
        $productBundleItem->setQuantity(2);

        $orderItem = $factory->createFromProductBundleItem($productBundleItem);
        $orderItemProductVariant = $orderItem->getProductVariant();

        self::assertEquals($productBundleItem, $orderItem->getProductBundleItem());
        self::assertSame($productBundleItem->getQuantity(), $orderItem->getQuantity());
        self::assertSame($productVariant->getCode(), $orderItemProductVariant->getCode());
    }
}

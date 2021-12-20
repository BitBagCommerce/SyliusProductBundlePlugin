<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
            ->expects($this->any())
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

        $this->assertEquals($productBundleItem, $orderItem->getProductBundleItem());
        $this->assertSame($productBundleItem->getQuantity(), $orderItem->getQuantity());
        $this->assertSame($productVariant->getCode(), $orderItemProductVariant->getCode());
    }
}

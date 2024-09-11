<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Provider;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommandInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Factory\AddProductBundleItemToCartCommandFactoryInterface;
use BitBag\SyliusProductBundlePlugin\Provider\AddProductBundleItemToCartCommandProvider;
use BitBag\SyliusProductBundlePlugin\Provider\AddProductBundleItemToCartCommandProviderInterface;
use BitBag\SyliusProductBundlePlugin\Repository\ProductBundleRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class AddProductBundleItemToCartCommandProviderTest extends TestCase
{
    private AddProductBundleItemToCartCommandFactoryInterface|MockObject $addProductBundleItemToCartCommandFactory;

    private ProductBundleRepositoryInterface|MockObject $productBundleRepository;

    private ProductVariantRepositoryInterface|MockObject $productVariantRepository;

    private ProductBundleInterface|MockObject $bundle;

    private ProductBundleItemInterface|MockObject $bundleItem1;

    private ProductBundleItemInterface|MockObject $bundleItem2;

    private AddProductBundleItemToCartCommandProviderInterface $provider;

    public function setUp(): void
    {
        $this->addProductBundleItemToCartCommandFactory = $this->createMock(AddProductBundleItemToCartCommandFactoryInterface::class);
        $this->productBundleRepository = $this->createMock(ProductBundleRepositoryInterface::class);
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);

        $this->bundleItem1 = $this->createMock(ProductBundleItemInterface::class);
        $this->bundleItem2 = $this->createMock(ProductBundleItemInterface::class);
        $this->bundle = $this->createMock(ProductBundleInterface::class);
        $this->bundle
            ->expects(self::any())
            ->method('getProductBundleItems')
            ->willReturn(new ArrayCollection([$this->bundleItem1, $this->bundleItem2]));

        $this->provider = new AddProductBundleItemToCartCommandProvider(
            $this->addProductBundleItemToCartCommandFactory,
            $this->productBundleRepository,
            $this->productVariantRepository,
        );
    }

    public function testItThrowsExceptionIfBundleIsNotFound(): void
    {
        self::expectException(\Exception::class);
        self::expectExceptionMessage('Product bundle not found');

        $this->productBundleRepository
            ->expects(self::once())
            ->method('findOneByProductCode')
            ->with('BUNDLE_CODE')
            ->willReturn(null);

        $this->provider->provide('BUNDLE_CODE', []);
    }

    public function testItWillNotOverwriteIfBundleIsPacked(): void
    {
        $this->bundle
            ->expects(self::exactly(2))
            ->method('isPackedProduct')
            ->willReturn(true);

        $this->productBundleRepository
            ->expects(self::once())
            ->method('findOneByProductCode')
            ->with('BUNDLE_CODE')
            ->willReturn($this->bundle);

        $addProductBundleItemToCartCommand = $this->createMock(AddProductBundleItemToCartCommandInterface::class);

        $this->addProductBundleItemToCartCommandFactory
            ->expects(self::exactly(2))
            ->method('createNew')
            ->withConsecutive([$this->bundleItem1], [$this->bundleItem2])
            ->willReturn($addProductBundleItemToCartCommand);

        $this->productVariantRepository->expects(self::never())->method(self::anything());

        $this->provider->provide('BUNDLE_CODE', []);
    }

    public function testItWillNotOverwriteIfOverwrittenVariantsIsEmpty(): void
    {
        $this->bundle
            ->expects(self::exactly(2))
            ->method('isPackedProduct')
            ->willReturn(false);

        $this->productBundleRepository
            ->expects(self::once())
            ->method('findOneByProductCode')
            ->with('BUNDLE_CODE')
            ->willReturn($this->bundle);

        $addProductBundleItemToCartCommand = $this->createMock(AddProductBundleItemToCartCommandInterface::class);

        $this->addProductBundleItemToCartCommandFactory
            ->expects(self::exactly(2))
            ->method('createNew')
            ->withConsecutive([$this->bundleItem1], [$this->bundleItem2])
            ->willReturn($addProductBundleItemToCartCommand);

        $this->productVariantRepository->expects(self::never())->method(self::anything());

        $this->provider->provide('BUNDLE_CODE', []);
    }

    public function testItOverwrites(): void
    {
        $this->bundle
            ->expects(self::exactly(2))
            ->method('isPackedProduct')
            ->willReturn(false);

        $this->productBundleRepository
            ->expects(self::once())
            ->method('findOneByProductCode')
            ->with('BUNDLE_CODE')
            ->willReturn($this->bundle);

        $product = $this->createMock(ProductInterface::class);

        $oldProductVariant = $this->createMock(ProductVariantInterface::class);
        $oldProductVariant
            ->expects(self::once())
            ->method('getCode')
            ->willReturn('OLD_VARIANT_CODE');
        $oldProductVariant
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($product);

        $newProductVariant = $this->createMock(ProductVariantInterface::class);
        $newProductVariant
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($product);

        $this->bundleItem1
            ->expects(self::once())
            ->method('getProductVariant')
            ->willReturn($oldProductVariant);

        $this->productVariantRepository
            ->expects(self::exactly(3))
            ->method('findOneBy')
            ->willReturnOnConsecutiveCalls($oldProductVariant, $newProductVariant, $newProductVariant);

        $addProductBundleItemToCartCommand = $this->createMock(AddProductBundleItemToCartCommandInterface::class);

        $this->addProductBundleItemToCartCommandFactory
            ->expects(self::exactly(2))
            ->method('createNew')
            ->withConsecutive([$this->bundleItem1], [$this->bundleItem2])
            ->willReturn($addProductBundleItemToCartCommand);

        $overwrittenVariants = [
            [
                'from' => 'OLD_VARIANT_CODE',
                'to' => 'NEW_VARIANT_CODE',
            ],
        ];

        $this->provider->provide('BUNDLE_CODE', $overwrittenVariants);
    }
}

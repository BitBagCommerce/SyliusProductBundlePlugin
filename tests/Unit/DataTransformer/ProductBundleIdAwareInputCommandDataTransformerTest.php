<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\DataTransformer;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Command\ProductBundleIdAwareInterface;
use BitBag\SyliusProductBundlePlugin\DataTransformer\ProductBundleIdAwareInputCommandDataTransformer;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundle;
use PHPUnit\Framework\TestCase;
use stdClass;
use Webmozart\Assert\InvalidArgumentException;

final class ProductBundleIdAwareInputCommandDataTransformerTest extends TestCase
{
    private const OBJECT_TO_POPULATE_KEY = 'object_to_populate';

    public function testSupportOnlyProductBundleIdAwareInterfaceInstances(): void
    {
        $productBundleIdAwareObject = $this->createMock(ProductBundleIdAwareInterface::class);
        $plainObject = new stdClass();
        $dataTransformer = new ProductBundleIdAwareInputCommandDataTransformer();

        self::assertTrue($dataTransformer->supportsTransformation($productBundleIdAwareObject));
        self::assertFalse($dataTransformer->supportsTransformation($plainObject));
    }

    public function testThrowExceptionIfObjectDoesntImplementRequiredInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $plainObject = new stdClass();
        $dataTransformer = new ProductBundleIdAwareInputCommandDataTransformer();

        $dataTransformer->transform($plainObject, '');
    }

    public function testSetProductBundleIdFromObjectToPopulate(): void
    {
        $command = new AddProductBundleToCartCommand();
        $productBundle = $this->createMock(ProductBundle::class);
        $productBundle->expects(self::once())->method('getId')->willReturn(1000);
        $context = [
            self::OBJECT_TO_POPULATE_KEY => $productBundle,
        ];
        $dataTransformer = new ProductBundleIdAwareInputCommandDataTransformer();

        $transformedObject = $dataTransformer->transform($command, '', $context);

        self::assertSame(1000, $transformedObject->getProductBundleId());
    }
}

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

    /** @test */
    public function it_should_support_only_instances_implementing_required_interface(): void
    {
        $productBundleIdAwareObject = $this->createMock(ProductBundleIdAwareInterface::class);
        $plainObject = new stdClass();
        $dataTransformer = new ProductBundleIdAwareInputCommandDataTransformer();

        $this->assertTrue($dataTransformer->supportsTransformation($productBundleIdAwareObject));
        $this->assertFalse($dataTransformer->supportsTransformation($plainObject));
    }

    /** @test */
    public function it_should_throw_exception_while_transforming_if_instance_doesnt_implement_required_interface(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $plainObject = new stdClass();
        $dataTransformer = new ProductBundleIdAwareInputCommandDataTransformer();

        $dataTransformer->transform($plainObject, '');
    }

    /** @test */
    public function it_should_set_product_bundle_id_from_object_to_populate(): void
    {
        $command = new AddProductBundleToCartCommand();
        $productBundle = $this->createMock(ProductBundle::class);
        $productBundle->expects($this->once())->method('getId')->willReturn(1000);
        $context = [
            self::OBJECT_TO_POPULATE_KEY => $productBundle,
        ];
        $dataTransformer = new ProductBundleIdAwareInputCommandDataTransformer();

        $transformedObject = $dataTransformer->transform($command, '', $context);

        $this->assertSame(1000, $transformedObject->getProductBundleId());
    }
}

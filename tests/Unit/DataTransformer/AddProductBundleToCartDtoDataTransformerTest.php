<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\DataTransformer;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\DataTransformer\AddProductBundleToCartDtoDataTransformer;
use BitBag\SyliusProductBundlePlugin\Dto\Api\AddProductBundleToCartDto;
use PHPUnit\Framework\TestCase;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\Api\AddProductBundleToCartDtoMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\OrderMother;
use Webmozart\Assert\InvalidArgumentException;

final class AddProductBundleToCartDtoDataTransformerTest extends TestCase
{
    public function testThrowErrorIfObjectIsNotInstanceOfAddProductBundleToCartDto(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Expected an instance of %s. Got: stdClass', AddProductBundleToCartDto::class)
        );

        $object = new \stdClass();
        $dataTransformer = new AddProductBundleToCartDtoDataTransformer();

        $dataTransformer->transform($object, '');
    }

    public function testThrowIfObjectToPopulateDoesntExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value other than null.');

        $object = AddProductBundleToCartDtoMother::create('PRODUCT_CODE');
        $dataTransformer = new AddProductBundleToCartDtoDataTransformer();

        $dataTransformer->transform($object, '');
    }

    public function testReturnAddProductBundleToCart(): void
    {
        $object = AddProductBundleToCartDtoMother::create('PRODUCT_CODE', 2);
        $context = [
            AddProductBundleToCartDtoDataTransformer::OBJECT_TO_POPULATE => OrderMother::createWithId(3),
        ];
        $dataTransformer = new AddProductBundleToCartDtoDataTransformer();

        $addProductBundleToCartCommand = $dataTransformer->transform($object, '', $context);

        self::assertInstanceOf(AddProductBundleToCartCommand::class, $addProductBundleToCartCommand);
        self::assertSame('PRODUCT_CODE', $addProductBundleToCartCommand->getProductCode());
        self::assertSame(2, $addProductBundleToCartCommand->getQuantity());
        self::assertSame(3, $addProductBundleToCartCommand->getOrderId());
    }
}

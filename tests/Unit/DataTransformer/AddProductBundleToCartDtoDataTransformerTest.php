<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\DataTransformer;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommandInterface;
use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\DataTransformer\AddProductBundleToCartDtoDataTransformer;
use BitBag\SyliusProductBundlePlugin\Dto\Api\AddProductBundleToCartDto;
use BitBag\SyliusProductBundlePlugin\Provider\AddProductBundleItemToCartCommandProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\Api\AddProductBundleToCartDtoMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\OrderMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\TypeExceptionMessage;
use Webmozart\Assert\InvalidArgumentException;

final class AddProductBundleToCartDtoDataTransformerTest extends TestCase
{
    private AddProductBundleItemToCartCommandProviderInterface|MockObject $provider;

    private AddProductBundleItemToCartCommandInterface|MockObject $addProductBundleItemToCartCommand;

    public function setUp(): void
    {
        $this->provider = $this->createMock(AddProductBundleItemToCartCommandProviderInterface::class);
        $this->addProductBundleItemToCartCommand = $this->createMock(AddProductBundleItemToCartCommandInterface::class);
    }

    public function testThrowErrorIfObjectIsNotInstanceOfAddProductBundleToCartDto(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(TypeExceptionMessage::EXPECTED_INSTANCE_OF_X_GOT_Y, AddProductBundleToCartDto::class, \stdClass::class),
        );

        $object = new \stdClass();
        $this->provider->expects(self::never())->method(self::anything());
        $dataTransformer = new AddProductBundleToCartDtoDataTransformer($this->provider);

        $dataTransformer->transform($object, '');
    }

    public function testThrowIfObjectToPopulateDoesntExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(TypeExceptionMessage::EXPECTED_VALUE_OTHER_THAN_NULL);

        $object = AddProductBundleToCartDtoMother::create('PRODUCT_CODE');
        $this->provider->expects(self::never())->method(self::anything());
        $dataTransformer = new AddProductBundleToCartDtoDataTransformer($this->provider);

        $dataTransformer->transform($object, '');
    }

    public function testReturnAddProductBundleToCart(): void
    {
        $object = AddProductBundleToCartDtoMother::create('PRODUCT_CODE', 2);
        $context = [
            AddProductBundleToCartDtoDataTransformer::OBJECT_TO_POPULATE => OrderMother::createWithId(3),
        ];

        $addProductBundleItemToCartCommands = new ArrayCollection([$this->addProductBundleItemToCartCommand]);

        $this->provider
            ->expects(self::once())
            ->method('provide')
            ->with('PRODUCT_CODE', [])
            ->willReturn($addProductBundleItemToCartCommands);

        $dataTransformer = new AddProductBundleToCartDtoDataTransformer($this->provider);

        $addProductBundleToCartCommand = $dataTransformer->transform($object, '', $context);

        self::assertInstanceOf(AddProductBundleToCartCommand::class, $addProductBundleToCartCommand);
        self::assertSame('PRODUCT_CODE', $addProductBundleToCartCommand->getProductCode());
        self::assertSame(2, $addProductBundleToCartCommand->getQuantity());
        self::assertSame(3, $addProductBundleToCartCommand->getOrderId());
        self::assertSame($addProductBundleItemToCartCommands, $addProductBundleToCartCommand->getProductBundleItems());
    }
}

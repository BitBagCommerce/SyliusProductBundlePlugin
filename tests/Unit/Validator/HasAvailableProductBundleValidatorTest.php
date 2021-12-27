<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Validator;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Validator\HasAvailableProductBundle;
use BitBag\SyliusProductBundlePlugin\Validator\HasAvailableProductBundleValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ChannelMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\OrderMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductVariantMother;

final class HasAvailableProductBundleValidatorTest extends ConstraintValidatorTestCase
{
    private const ORDER_ID = 5;

    private const PRODUCT_CODE = 'MY_PROD';

    private const CHANNEL_NAME = 'My Awesome Channel';

    /** @var mixed|MockObject|ProductRepositoryInterface */
    private $productRepository;

    /** @var mixed|MockObject|OrderRepositoryInterface */
    private $orderRepository;

    /** @var mixed|MockObject|AvailabilityCheckerInterface */
    private $availabilityChecker;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);

        parent::setUp();
    }

    public function testAddViolationIfProductIsDisabled(): void
    {
        $this->productRepository->expects(self::once())
            ->method('findOneByCode')
            ->with(self::PRODUCT_CODE)
            ->willReturn(ProductMother::createDisabledWithCode(self::PRODUCT_CODE))
        ;

        $command = new AddProductBundleToCartCommand(self::ORDER_ID, self::PRODUCT_CODE);
        $constraint = new HasAvailableProductBundle();

        $this->validator->validate($command, $constraint);

        $this->buildViolation(HasAvailableProductBundle::PRODUCT_DISABLED_MESSAGE)
            ->setParameter('{{ code }}', self::PRODUCT_CODE)
            ->assertRaised()
        ;
    }

    public function testAddViolationIfProductVariantDisabled(): void
    {
        $productVariant = ProductVariantMother::createDisabledWithCode(self::PRODUCT_CODE);
        $product = ProductMother::createWithProductVariantAndCode($productVariant, self::PRODUCT_CODE);

        $this->productRepository->expects(self::once())
            ->method('findOneByCode')
            ->with(self::PRODUCT_CODE)
            ->willReturn($product)
        ;

        $command = new AddProductBundleToCartCommand(self::ORDER_ID, self::PRODUCT_CODE);
        $constraint = new HasAvailableProductBundle();

        $this->validator->validate($command, $constraint);

        $this->buildViolation(HasAvailableProductBundle::PRODUCT_VARIANT_DISABLED_MESSAGE)
            ->setParameter('{{ code }}', self::PRODUCT_CODE)
            ->assertRaised()
        ;
    }

    public function testAddViolationIfCartChannelAndProductChannelAreDifferent(): void
    {
        $productVariant = ProductVariantMother::createWithCode(self::PRODUCT_CODE);
        $product = ProductMother::createWithProductVariantAndCode($productVariant, self::PRODUCT_CODE);
        $this->productRepository->method('findOneByCode')
            ->with(self::PRODUCT_CODE)
            ->willReturn($product)
        ;

        $channel = ChannelMother::createWithName(self::CHANNEL_NAME);
        $cart = OrderMother::createWithChannel($channel);
        $this->orderRepository->expects(self::once())
            ->method('findCartById')
            ->with(self::ORDER_ID)
            ->willReturn($cart)
        ;

        $command = new AddProductBundleToCartCommand(self::ORDER_ID, self::PRODUCT_CODE);
        $constraint = new HasAvailableProductBundle();

        $this->validator->validate($command, $constraint);

        $this->buildViolation(HasAvailableProductBundle::PRODUCT_DOESNT_EXIST_IN_CHANNEL_MESSAGE)
            ->setParameter('{{ channel }}', self::CHANNEL_NAME)
            ->setParameter('{{ code }}', self::PRODUCT_CODE)
            ->assertRaised()
        ;
    }

    public function testAddViolationIfNewProductQuantityInTheCartExceedsStock(): void
    {
        $channel = ChannelMother::createWithName(self::CHANNEL_NAME);
        $productVariant = ProductVariantMother::createWithCode(self::PRODUCT_CODE);
        $product = ProductMother::createWithChannelAndProductVariantAndCode(
            $channel,
            $productVariant,
            self::PRODUCT_CODE
        );
        $this->productRepository->method('findOneByCode')
            ->with(self::PRODUCT_CODE)
            ->willReturn($product)
        ;

        $cart = OrderMother::createWithChannel($channel);
        $this->orderRepository->method('findCartById')
            ->with(self::ORDER_ID)
            ->willReturn($cart)
        ;

        $this->availabilityChecker->expects(self::once())
            ->method('isStockSufficient')
            ->with($productVariant, 1)
            ->willReturn(false)
        ;

        $command = new AddProductBundleToCartCommand(self::ORDER_ID, self::PRODUCT_CODE);
        $constraint = new HasAvailableProductBundle();

        $this->validator->validate($command, $constraint);

        $this->buildViolation(HasAvailableProductBundle::PRODUCT_VARIANT_INSUFFICIENT_STOCK_MESSAGE)
            ->setParameter('{{ code }}', self::PRODUCT_CODE)
            ->assertRaised()
        ;
    }

    protected function createValidator(): HasAvailableProductBundleValidator
    {
        return new HasAvailableProductBundleValidator(
            $this->productRepository,
            $this->orderRepository,
            $this->availabilityChecker
        );
    }
}

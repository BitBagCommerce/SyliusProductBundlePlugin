<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Validator;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Validator\ValidAddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Validator\ValidAddProductBundleToCartCommandValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Webmozart\Assert\InvalidArgumentException;

/**
 * @property AddProductBundleToCartCommand $value
 * @property ValidAddProductBundleToCartCommand $constraint
 */
final class ValidAddProductBundleToCartCommandValidatorTest extends ConstraintValidatorTestCase
{
    /** @var ObjectRepository|mixed|MockObject */
    private $productBundleRepository;
    /** @var mixed|MockObject|OrderRepositoryInterface  */
    private $orderRepository;
    /** @var mixed|MockObject|AvailabilityCheckerInterface  */
    private $availabilityChecker;

    protected function setUp(): void
    {
        $this->productBundleRepository = $this->createMock(ObjectRepository::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->value = new AddProductBundleToCartCommand();
        $this->constraint = new ValidAddProductBundleToCartCommand();

        parent::setUp();
    }

    protected function createValidator(): ValidAddProductBundleToCartCommandValidator
    {
        return new ValidAddProductBundleToCartCommandValidator(
            $this->productBundleRepository,
            $this->orderRepository,
            $this->availabilityChecker
        );
    }

    /** @test */
    public function it_should_throw_exception_if_value_is_not_an_add_product_bundle_to_cart_command_instance(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate(new \stdClass(), new ValidAddProductBundleToCartCommand());
    }

    /** @test */
    public function it_should_throw_exception_if_constraint_is_not_an_valid_add_product_bundle_to_cart_command_constraint_instance(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $constraint = $this->createMock(Constraint::class);

        $this->validator->validate(new AddProductBundleToCartCommand(), $constraint);
    }

    /** @test */
    public function it_should_add_violation_if_both_order_id_and_token_are_null(): void
    {
        $this->validator->validate(new AddProductBundleToCartCommand(), new ValidAddProductBundleToCartCommand());

        $this->buildViolation(ValidAddProductBundleToCartCommand::NO_ORDER_ID_OR_TOKEN_MESSAGE)->assertRaised();
    }

    /** @test */
    public function it_should_add_violation_if_product_bundle_if_product_bundle_doesnt_exist(): void
    {
        $value = new AddProductBundleToCartCommand();
        $value->setProductBundleId(1);
        $value->setOrderId(2);
        $this->productBundleRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn(null)
        ;

        $this->validator->validate($value, new ValidAddProductBundleToCartCommand());

        $this->buildViolation(ValidAddProductBundleToCartCommand::PRODUCT_BUNDLE_DOESNT_EXIST_MESSAGE)
            ->setParameter('{{ id }}', "1")
            ->assertRaised()
        ;
    }

    /** @test */
    public function it_should_add_violation_if_product_related_to_product_bundle_is_disabled(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $product->expects(self::once())->method('isEnabled')->willReturn(false);
        $product->expects(self::once())->method('getCode')->willReturn("WHISKEY_PACK");

        $productBundle = $this->createMock(ProductBundleInterface::class);
        $productBundle->expects(self::once())->method('getProduct')->willReturn($product);

        $value = new AddProductBundleToCartCommand();
        $value->setProductBundleId(1);
        $value->setOrderId(2);

        $this->productBundleRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($productBundle)
        ;

        $this->validator->validate($value, new ValidAddProductBundleToCartCommand());

        $this->buildViolation(ValidAddProductBundleToCartCommand::PRODUCT_DISABLED_MESSAGE)
            ->setParameter('{{ code }}', "WHISKEY_PACK")
            ->assertRaised()
        ;
    }

    /** @test */
    public function it_should_add_violation_if_product_variant_related_to_is_disabled(): void
    {
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $productVariant->expects(self::once())->method('isEnabled')->willReturn(false);
        $productVariant->expects(self::once())->method('getCode')->willReturn("WHISKEY_PACK");

        $variantsCollection = new ArrayCollection([$productVariant]);

        $product = $this->createMock(ProductInterface::class);
        $product->expects(self::once())->method('isEnabled')->willReturn(true);
        $product->expects(self::once())->method('getVariants')->willReturn($variantsCollection);

        $productBundle = $this->createMock(ProductBundleInterface::class);
        $productBundle->expects(self::once())->method('getProduct')->willReturn($product);

        $value = new AddProductBundleToCartCommand();
        $value->setProductBundleId(1);
        $value->setOrderId(2);

        $this->productBundleRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($productBundle)
        ;

        $this->validator->validate($value, new ValidAddProductBundleToCartCommand());

        $this->buildViolation(ValidAddProductBundleToCartCommand::PRODUCT_VARIANT_DISABLED_MESSAGE)
            ->setParameter('{{ code }}', "WHISKEY_PACK")
            ->assertRaised()
        ;
    }

    /** @test */
    public function it_should_add_violation_if_cart_doesnt_exist(): void
    {
        $this->orderRepository->expects(self::once())
            ->method('findCartById')
            ->with(2)
            ->willReturn(null)
        ;

        $productVariant = $this->createMock(ProductVariantInterface::class);
        $productVariant->expects(self::once())->method('isEnabled')->willReturn(true);

        $variantsCollection = new ArrayCollection([$productVariant]);

        $product = $this->createMock(ProductInterface::class);
        $product->expects(self::once())->method('isEnabled')->willReturn(true);
        $product->expects(self::once())->method('getVariants')->willReturn($variantsCollection);

        $productBundle = $this->createMock(ProductBundleInterface::class);
        $productBundle->expects(self::once())->method('getProduct')->willReturn($product);

        $value = new AddProductBundleToCartCommand();
        $value->setProductBundleId(1);
        $value->setOrderId(2);

        $this->productBundleRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($productBundle)
        ;

        $this->validator->validate($value, new ValidAddProductBundleToCartCommand());

        $this->buildViolation(ValidAddProductBundleToCartCommand::CART_DOESNT_EXIST_MESSAGE)
            ->assertRaised()
        ;
    }

    /** @test */
    public function it_should_add_violation_if_insufficient_stock(): void
    {
        $this->availabilityChecker->expects(self::once())->method('isStockSufficient')->willReturn(false);

        $cart = $this->createMock(OrderInterface::class);
        $cart->expects(self::once())->method('getItems')->willReturn(new ArrayCollection());

        $this->orderRepository->expects(self::once())
            ->method('findCartById')
            ->with(2)
            ->willReturn($cart)
        ;

        $productVariant = $this->createMock(ProductVariantInterface::class);
        $productVariant->expects(self::once())->method('isEnabled')->willReturn(true);
        $productVariant->expects(self::once())->method('getCode')->willReturn("WHISKEY_PACK");

        $variantsCollection = new ArrayCollection([$productVariant]);

        $product = $this->createMock(ProductInterface::class);
        $product->expects(self::once())->method('isEnabled')->willReturn(true);
        $product->expects(self::once())->method('getVariants')->willReturn($variantsCollection);

        $productBundle = $this->createMock(ProductBundleInterface::class);
        $productBundle->expects(self::once())->method('getProduct')->willReturn($product);

        $value = new AddProductBundleToCartCommand();
        $value->setProductBundleId(1);
        $value->setOrderId(2);

        $this->productBundleRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($productBundle)
        ;

        $this->validator->validate($value, new ValidAddProductBundleToCartCommand());

        $this->buildViolation(ValidAddProductBundleToCartCommand::PRODUCT_VARIANT_INSUFFICIENT_STOCK_MESSAGE)
            ->setParameter('{{ code }}', "WHISKEY_PACK")
            ->assertRaised()
        ;
    }

    /** @test */
    public function it_should_add_violation_if_cart_and_product_channels_are_different(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->availabilityChecker->expects(self::once())->method('isStockSufficient')->willReturn(true);

        $cart = $this->createMock(OrderInterface::class);
        $cart->expects(self::once())->method('getItems')->willReturn(new ArrayCollection());
        $cart->expects(self::once())->method('getChannel')->willReturn($channel);

        $this->orderRepository->expects(self::once())
            ->method('findCartById')
            ->with(2)
            ->willReturn($cart)
        ;

        $productVariant = $this->createMock(ProductVariantInterface::class);
        $productVariant->expects(self::once())->method('isEnabled')->willReturn(true);

        $variantsCollection = new ArrayCollection([$productVariant]);

        $product = $this->createMock(ProductInterface::class);
        $product->expects(self::once())->method('isEnabled')->willReturn(true);
        $product->expects(self::once())->method('getVariants')->willReturn($variantsCollection);
        $product->expects(self::once())->method('getName')->willReturn("Whiskey Pack");
        $product->expects(self::once())->method('hasChannel')->willReturn(false);

        $productBundle = $this->createMock(ProductBundleInterface::class);
        $productBundle->expects(self::once())->method('getProduct')->willReturn($product);

        $value = new AddProductBundleToCartCommand();
        $value->setProductBundleId(1);
        $value->setOrderId(2);

        $this->productBundleRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($productBundle)
        ;

        $this->validator->validate($value, new ValidAddProductBundleToCartCommand());

        $this->buildViolation(ValidAddProductBundleToCartCommand::PRODUCT_DOESNT_EXIST_MESSAGE)
            ->setParameter('{{ name }}', "Whiskey Pack")
            ->assertRaised()
        ;
    }
}

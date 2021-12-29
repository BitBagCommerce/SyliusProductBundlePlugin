<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Validator;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Validator\HasProductBundle;
use BitBag\SyliusProductBundlePlugin\Validator\HasProductBundleValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductBundleMother;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductMother;

final class HasProductBundleTest extends ConstraintValidatorTestCase
{
    private const ORDER_ID = 5;

    private const PRODUCT_CODE = 'MY_PRODUCT';

    /** @var mixed|MockObject|OrderRepositoryInterface */
    private $productRepository;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);

        parent::setUp();
    }

    public function testThrowExceptionIfValueIsNotImplementingProductCodeAwareInterface(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $value = new \stdClass();
        $constraint = new HasProductBundle();

        $this->validator->validate($value, $constraint);
    }

    public function testAddViolationIfProductDoesntExist(): void
    {
        $this->productRepository->expects(self::once())
            ->method('findOneByCode')
            ->with(self::PRODUCT_CODE)
            ->willReturn(null)
        ;

        $value = new AddProductBundleToCartCommand(self::ORDER_ID, self::PRODUCT_CODE);
        $constraint = new HasProductBundle();

        $this->validator->validate($value, $constraint);

        $this->buildViolation(HasProductBundle::PRODUCT_DOESNT_EXIST_MESSAGE)->assertRaised();
    }

    public function testAddViolationIfProductIsNotProductBundle(): void
    {
        $product = ProductMother::create();
        $this->productRepository->expects(self::once())
            ->method('findOneByCode')
            ->with(self::PRODUCT_CODE)
            ->willReturn($product)
        ;

        $value = new AddProductBundleToCartCommand(self::ORDER_ID, self::PRODUCT_CODE);
        $constraint = new HasProductBundle();

        $this->validator->validate($value, $constraint);

        $this->buildViolation(HasProductBundle::NOT_A_BUNDLE_MESSAGE)->assertRaised();
    }

    public function testNoViolationIfProductIsProductBundle(): void
    {
        $productBundle = ProductBundleMother::create();
        $product = ProductMother::createWithBundle($productBundle);
        $this->productRepository->expects(self::once())
            ->method('findOneByCode')
            ->with(self::PRODUCT_CODE)
            ->willReturn($product)
        ;

        $value = new AddProductBundleToCartCommand(self::ORDER_ID, self::PRODUCT_CODE);
        $constraint = new HasProductBundle();

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    protected function createValidator(): HasProductBundleValidator
    {
        return new HasProductBundleValidator($this->productRepository);
    }
}
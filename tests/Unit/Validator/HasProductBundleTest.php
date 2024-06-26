<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Validator;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
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

    /**
     * @dataProvider pessimisticDataProvider
     */
    public function testPessimisticCase(?ProductInterface $product, ?string $violationMessage): void
    {
        $this->productRepository->expects(self::once())
            ->method('findOneByCode')
            ->with(self::PRODUCT_CODE)
            ->willReturn($product)
        ;

        $value = new AddProductBundleToCartCommand(self::ORDER_ID, self::PRODUCT_CODE);
        $constraint = new HasProductBundle();

        $this->validator->validate($value, $constraint);

        if (null !== $violationMessage) {
            $this->buildViolation($violationMessage)->assertRaised();
        } else {
            $this->assertNoViolation();
        }
    }

    public function pessimisticDataProvider(): array
    {
        return [
            'product is a null' => [null, HasProductBundle::PRODUCT_DOESNT_EXIST_MESSAGE],
            'product is not a bundle' => [ProductMother::create(), HasProductBundle::NOT_A_BUNDLE_MESSAGE],
            'product is a bundle' => [ProductMother::createWithBundle(ProductBundleMother::create()), null],
        ];
    }

    protected function createValidator(): HasProductBundleValidator
    {
        return new HasProductBundleValidator($this->productRepository);
    }
}

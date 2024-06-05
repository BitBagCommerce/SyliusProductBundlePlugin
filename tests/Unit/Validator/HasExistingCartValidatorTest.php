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
use BitBag\SyliusProductBundlePlugin\Validator\HasExistingCart;
use BitBag\SyliusProductBundlePlugin\Validator\HasExistingCartValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\OrderMother;

final class HasExistingCartValidatorTest extends ConstraintValidatorTestCase
{
    private const ORDER_ID = 5;

    private const PRODUCT_CODE = 'MY_PRODUCT';

    /** @var mixed|MockObject|OrderRepositoryInterface */
    private $orderRepository;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);

        parent::setUp();
    }

    public function testThrowExceptionIfValueIsNotImplementingOrderIdentityAwareInterface(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $value = new \stdClass();
        $constraint = new HasExistingCart();

        $this->validator->validate($value, $constraint);
    }

    public function testQueryOrderFromRepositoryIfValueIsInt(): void
    {
        $this->orderRepository->expects(self::once())
            ->method('findCartById')
            ->with(5)
            ->willReturn(OrderMother::create())
        ;

        $value = new AddProductBundleToCartCommand(self::ORDER_ID, self::PRODUCT_CODE);
        $constraint = new HasExistingCart();

        $this->validator->validate($value, $constraint);
    }

    public function testAddViolationIfValueIsNull(): void
    {
        $this->orderRepository->method('findCartById')
            ->willReturn(null)
        ;

        $value = new AddProductBundleToCartCommand(self::ORDER_ID, self::PRODUCT_CODE);
        $constraint = new HasExistingCart();

        $this->validator->validate($value, $constraint);

        $this->buildViolation(HasExistingCart::CART_DOESNT_EXIST_MESSAGE)->assertRaised();
    }

    public function testAddViolationIfOrderHasNoId(): void
    {
        $value = new AddProductBundleToCartCommand(self::ORDER_ID, self::PRODUCT_CODE);
        $constraint = new HasExistingCart();

        $this->validator->validate($value, $constraint);

        $this->buildViolation(HasExistingCart::CART_DOESNT_EXIST_MESSAGE)->assertRaised();
    }

    protected function createValidator(): HasExistingCartValidator
    {
        return new HasExistingCartValidator($this->orderRepository);
    }
}

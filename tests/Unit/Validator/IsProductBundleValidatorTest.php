<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Validator;

use BitBag\SyliusProductBundlePlugin\Validator\IsProductBundle;
use BitBag\SyliusProductBundlePlugin\Validator\IsProductBundleValidator;
use Sylius\Component\Core\Model\Product as BaseProduct;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Tests\BitBag\SyliusProductBundlePlugin\Entity\Product;

final class IsProductBundleValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): IsProductBundleValidator
    {
        return new IsProductBundleValidator();
    }

    public function testThrowExceptionIfProductIsNotProductBundleAware(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $notBundleAwareProduct = new BaseProduct();
        $constraint = new IsProductBundle();

        $this->validator->validate($notBundleAwareProduct, $constraint);
    }

    public function testAddViolationIfProductIsNotBundle(): void
    {
        $product = new Product();
        $constraint = new IsProductBundle();

        $this->validator->validate($product, $constraint);

        $this->buildViolation(IsProductBundle::NOT_A_BUNDLE)->assertRaised();
    }
}

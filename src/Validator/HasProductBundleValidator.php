<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Validator;

use BitBag\SyliusProductBundlePlugin\Command\ProductCodeAwareInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Webmozart\Assert\Assert;

final class HasProductBundleValidator extends ConstraintValidator
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * @param ProductCodeAwareInterface|mixed $value
     * @param HasProductBundle|Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, HasProductBundle::class);

        if (!$value instanceof ProductCodeAwareInterface) {
            throw new UnexpectedValueException($value, ProductCodeAwareInterface::class);
        }

        /** @var ProductInterface|null $product */
        $product = $this->productRepository->findOneByCode($value->getProductCode());

        if (null === $product) {
            $this->context->addViolation(HasProductBundle::PRODUCT_DOESNT_EXIST_MESSAGE);

            return;
        }

        if (!$product->isBundle()) {
            $this->context->addViolation(HasProductBundle::NOT_A_BUNDLE_MESSAGE);

            return;
        }
    }
}

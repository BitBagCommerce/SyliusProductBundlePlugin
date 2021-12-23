<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Validator;

use BitBag\SyliusProductBundlePlugin\Command\ProductBundleIdAwareInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class HasValidProductBundleIdentifierValidator extends ConstraintValidator
{
    /** @var RepositoryInterface */
    private $productBundleRepository;

    public function __construct(RepositoryInterface $productBundleRepository)
    {
        $this->productBundleRepository = $productBundleRepository;
    }

    /**
     * @param ProductBundleIdAwareInterface|mixed $value
     * @param HasValidProductBundleIdentifier|Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, ProductBundleIdAwareInterface::class);
        Assert::isInstanceOf($constraint, HasValidProductBundleIdentifier::class);

        /** @var ProductBundleInterface|null $productBundle */
        $productBundle = $this->productBundleRepository->find($value->getProductBundleId());
        if (null === $productBundle) {
            $this->context->addViolation(HasValidProductBundleIdentifier::PRODUCT_BUNDLE_DOESNT_EXIST_MESSAGE, [
                '{{ id }}' => $value->getProductBundleId(),
            ]);
        }

        $product = $productBundle->getProduct();
        if (!$product->isEnabled()) {
            $this->context->addViolation(HasValidProductBundleIdentifier::PRODUCT_DISABLED_MESSAGE, [
                '{{ code }}' => $product->getCode(),
            ]);
        }

        if ($product->getVariants()->isEmpty()) {
            $this->context->addViolation(HasValidProductBundleIdentifier::PRODUCT_VARIANT_DOESNT_EXIST_MESSAGE, [
                '{{ code }}' => $product->getCode(),
            ]);
        }

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $product->getVariants()->first();
        if (false === $productVariant->isEnabled()) {
            $this->context->addViolation(HasValidProductBundleIdentifier::PRODUCT_VARIANT_DISABLED_MESSAGE, [
                '{{ code }}' => $productVariant->getCode(),
            ]);
        }
    }
}

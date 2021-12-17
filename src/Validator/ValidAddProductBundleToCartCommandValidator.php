<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Validator;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Doctrine\Persistence\ObjectRepository;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ValidAddProductBundleToCartCommandValidator extends ConstraintValidator
{
    /** @var ObjectRepository */
    private $productBundleRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    public function __construct(
        ObjectRepository $productBundleRepository,
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker
    ) {
        $this->productBundleRepository = $productBundleRepository;
        $this->orderRepository = $orderRepository;
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * @param AddProductBundleToCartCommand|mixed $value
     * @param ValidAddProductBundleToCartCommand|Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, AddProductBundleToCartCommand::class);
        Assert::isInstanceOf($constraint, ValidAddProductBundleToCartCommand::class);

        if (null === $value->getOrderId() && null === $value->getOrderToken()) {
            $this->context->addViolation(
                ValidAddProductBundleToCartCommand::NO_ORDER_ID_OR_TOKEN_MESSAGE
            );

            return;
        }

        /** @var ProductBundleInterface|null $productBundle */
        $productBundle = $this->productBundleRepository->find($value->getProductBundleId());

        if (null === $productBundle) {
            $this->context->addViolation(
                ValidAddProductBundleToCartCommand::PRODUCT_BUNDLE_DOESNT_EXIST_MESSAGE,
                [
                    '{{ id }}' => $value->getProductBundleId(),
                ]
            );

            return;
        }

        /** @var ProductInterface $product */
        $product = $productBundle->getProduct();
        if (!$product->isEnabled()) {
            $this->context->addViolation(
                ValidAddProductBundleToCartCommand::PRODUCT_DISABLED_MESSAGE,
                [
                    '{{ code }}' => $product->getCode(),
                ]
            );

            return;
        }

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $product->getVariants()->first();
        if (!$productVariant->isEnabled()) {
            $this->context->addViolation(
                ValidAddProductBundleToCartCommand::PRODUCT_VARIANT_DISABLED_MESSAGE,
                [
                    '{{ code }}' => $productVariant->getCode(),
                ]
            );

            return;
        }

        /** @var \Sylius\Component\Core\Model\OrderInterface|null $cart */
        $cart = $this->getCart($value);
        if (null === $cart) {
            $this->context->addViolation(
                $constraint::CART_DOESNT_EXIST_MESSAGE
            );

            return;
        }

        $targetQuantity = $value->getQuantity() + $this->getCurrentProductVariantQuantityFromCart($cart, $productVariant);
        if (!$this->availabilityChecker->isStockSufficient($productVariant, $targetQuantity)) {
            $this->context->addViolation(
                ValidAddProductBundleToCartCommand::PRODUCT_VARIANT_INSUFFICIENT_STOCK_MESSAGE,
                [
                    '{{ code }}' => $productVariant->getCode(),
                ]
            );

            return;
        }

        $channel = $cart->getChannel();
        Assert::notNull($channel);

        if (!$product->hasChannel($channel)) {
            $this->context->addViolation(
                ValidAddProductBundleToCartCommand::PRODUCT_DOESNT_EXIST_MESSAGE,
                [
                    '{{ name }}' => $product->getName(),
                ]
            );
        }
    }

    private function getCart(AddProductBundleToCartCommand $value): ?OrderInterface
    {
        if (null !== $value->getOrderToken()) {
            return $this->orderRepository->findCartByTokenValue($value->getOrderToken());
        }

        return $this->orderRepository->findCartById($value->getOrderId());
    }

    private function getCurrentProductVariantQuantityFromCart(OrderInterface $cart, ProductVariantInterface $productVariant): int
    {
        /** @var OrderItemInterface $item */
        foreach ($cart->getItems() as $item) {
            /** @var ProductVariantInterface $itemProductVariant */
            $itemProductVariant = $item->getVariant();

            if ($productVariant->isTracked() && $itemProductVariant->getCode() === $productVariant->getCode()) {
                return $item->getQuantity();
            }
        }

        return 0;
    }
}

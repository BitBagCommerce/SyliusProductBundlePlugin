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
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
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

        if (false === $this->validateOrderTokenAndId($value)) {
            return;
        }

        /** @var ProductBundleInterface $productBundle */
        $productBundle = $this->productBundleRepository->find($value->getProductBundleId());
        if (false === $this->validateProductBundle($productBundle, $value)) {
            return;
        }

        /** @var ProductInterface $product */
        $product = $productBundle->getProduct();
        if (false === $this->validateIsProductEnabled($product)) {
            return;
        }

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $product->getVariants()->first();
        if (false === $this->validateProductVariant($productVariant)) {
            return;
        }

        /** @var OrderInterface $cart */
        $cart = $this->getCart($value);
        if (false === $this->validateCart($cart)) {
            return;
        }

        if (false === $this->validateProductStock($cart, $productVariant, $value)) {
            return;
        }

        /** @var ChannelInterface $channel */
        $channel = $cart->getChannel();
        $this->validateChannel($product, $channel);
    }

    private function getCart(AddProductBundleToCartCommand $value): ?BaseOrderInterface
    {
        /** @var OrderInterface|null $cart */
        $cart = null;

        if (null !== $value->getOrderToken()) {
            $cart = $this->orderRepository->findCartByTokenValue($value->getOrderToken());
        }

        if (null === $cart) {
            $cart = $this->orderRepository->findCartById($value->getOrderId());
        }

        return $cart;
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

    private function validateOrderTokenAndId(AddProductBundleToCartCommand $value): bool
    {
        if (null !== $value->getOrderId() || null !== $value->getOrderToken()) {
            return true;
        }

        $this->context->addViolation(
            ValidAddProductBundleToCartCommand::NO_ORDER_ID_OR_TOKEN_MESSAGE
        );

        return false;
    }

    private function validateProductBundle(?ProductBundleInterface $productBundle, AddProductBundleToCartCommand $value): bool
    {
        if (null !== $productBundle) {
            return true;
        }

        $this->context->addViolation(
            ValidAddProductBundleToCartCommand::PRODUCT_BUNDLE_DOESNT_EXIST_MESSAGE,
            [
                '{{ id }}' => $value->getProductBundleId(),
            ]
        );

        return false;
    }

    private function validateIsProductEnabled(ProductInterface $product): bool
    {
        if ($product->isEnabled()) {
            return true;
        }

        $this->context->addViolation(
            ValidAddProductBundleToCartCommand::PRODUCT_DISABLED_MESSAGE,
            [
                '{{ code }}' => $product->getCode(),
            ]
        );

        return false;
    }

    private function validateProductVariant(ProductVariantInterface $productVariant): bool
    {
        if ($productVariant->isEnabled()) {
            return true;
        }

        $this->context->addViolation(
            ValidAddProductBundleToCartCommand::PRODUCT_VARIANT_DISABLED_MESSAGE,
            [
                '{{ code }}' => $productVariant->getCode(),
            ]
        );

        return false;
    }

    private function validateCart(?OrderInterface $cart): bool
    {
        if (null !== $cart) {
            return true;
        }

        $this->context->addViolation(
            ValidAddProductBundleToCartCommand::CART_DOESNT_EXIST_MESSAGE
        );

        return false;
    }

    private function validateProductStock(
        OrderInterface $cart,
        ProductVariantInterface $productVariant,
        AddProductBundleToCartCommand $value
    ): bool {
        $targetQuantity = $value->getQuantity() + $this->getCurrentProductVariantQuantityFromCart($cart, $productVariant);
        if ($this->availabilityChecker->isStockSufficient($productVariant, $targetQuantity)) {
            return true;
        }

        $this->context->addViolation(
            ValidAddProductBundleToCartCommand::PRODUCT_VARIANT_INSUFFICIENT_STOCK_MESSAGE,
            [
                '{{ code }}' => $productVariant->getCode(),
            ]
        );

        return false;
    }

    private function validateChannel(ProductInterface $product, ChannelInterface $channel): bool
    {
        if ($product->hasChannel($channel)) {
            return true;
        }

        $this->context->addViolation(
            ValidAddProductBundleToCartCommand::PRODUCT_DOESNT_EXIST_MESSAGE,
            [
                '{{ name }}' => $product->getName(),
            ]
        );

        return false;
    }
}

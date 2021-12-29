<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Validator;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class HasAvailableProductBundleValidator extends ConstraintValidator
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker
    ) {
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * @param AddProductBundleToCartCommand|mixed $value
     * @param HasAvailableProductBundle|Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, AddProductBundleToCartCommand::class);
        Assert::isInstanceOf($constraint, HasAvailableProductBundle::class);

        $product = $this->productRepository->findOneByCode($value->getProductCode());
        Assert::notNull($product);

        if (!$product->isEnabled()) {
            $this->context->addViolation(HasAvailableProductBundle::PRODUCT_DISABLED_MESSAGE, [
                '{{ code }}' => $product->getCode(),
            ]);

            return;
        }

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $product->getVariants()->first();
        if (!$productVariant->isEnabled()) {
            $this->context->addViolation(HasAvailableProductBundle::PRODUCT_VARIANT_DISABLED_MESSAGE, [
                '{{ code }}' => $productVariant->getCode(),
            ]);

            return;
        }

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartById($value->getOrderId());
        Assert::notNull($cart);

        /** @var ChannelInterface $channel */
        $channel = $cart->getChannel();
        if (!$product->hasChannel($channel)) {
            $this->context->addViolation(HasAvailableProductBundle::PRODUCT_DOESNT_EXIST_IN_CHANNEL_MESSAGE, [
                '{{ channel }}' => $channel->getName(),
                '{{ code }}' => $product->getCode(),
            ]);

            return;
        }

        $targetQuantity = $value->getQuantity() + $this->getCurrentProductVariantQuantityFromCart($cart, $productVariant);
        if ($this->availabilityChecker->isStockSufficient($productVariant, $targetQuantity)) {
            return;
        }

        $this->context->addViolation(
            HasAvailableProductBundle::PRODUCT_VARIANT_INSUFFICIENT_STOCK_MESSAGE,
            [
                '{{ code }}' => $productVariant->getCode(),
            ]
        );
    }

    private function getCurrentProductVariantQuantityFromCart(
        OrderInterface $cart,
        ProductVariantInterface $productVariant
    ): int {
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

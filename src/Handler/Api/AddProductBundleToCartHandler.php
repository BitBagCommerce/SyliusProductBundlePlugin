<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Handler\Api;

use BitBag\SyliusProductBundlePlugin\Dto\Api\AddProductBundleToCartDto;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler\CartProcessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class AddProductBundleToCartHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ProductRepositoryInterface $productRepository,
        private CartProcessorInterface $cartProcessor,
    ) {
    }

    public function __invoke(AddProductBundleToCartDto $addProductBundleToCartCommand): OrderInterface
    {
        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($addProductBundleToCartCommand->getOrderTokenValue());

        if (null === $cart) {
            throw new \InvalidArgumentException('Cart with given token has not been found.');
        }

        /** @var ProductInterface|null $product */
        $product = $this->productRepository->findOneByCode($addProductBundleToCartCommand->getProductCode());
        Assert::notNull($product);
        Assert::true($product->isBundle());

        /** @var ProductBundleInterface|null $productBundle */
        $productBundle = $product->getProductBundle();
        Assert::notNull($productBundle);

        $quantity = $addProductBundleToCartCommand->getQuantity();
        Assert::greaterThan($quantity, 0);

        $this->cartProcessor->process($cart, $productBundle, $quantity);

        return $cart;
    }
}

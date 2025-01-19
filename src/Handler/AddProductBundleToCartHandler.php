<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Handler;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler\CartProcessorInterface;
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

    public function __invoke(AddProductBundleToCartCommand $addProductBundleToCartCommand): void
    {
        $cart = $this->orderRepository->findCartById($addProductBundleToCartCommand->getOrderId());
        Assert::notNull($cart);

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
        $this->orderRepository->add($cart);
    }
}

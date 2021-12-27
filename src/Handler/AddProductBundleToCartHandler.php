<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Handler;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler\CartProcessorInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class AddProductBundleToCartHandler implements MessageHandlerInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var CartProcessorInterface */
    private $cartProcessor;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        CartProcessorInterface $cartItemProcessor
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->cartProcessor = $cartItemProcessor;
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
        $quantity = $addProductBundleToCartCommand->getQuantity();
        Assert::greaterThan($quantity, 0);

        $this->cartProcessor->process($cart, $productBundle, $quantity);
        $this->orderRepository->add($cart);
    }
}

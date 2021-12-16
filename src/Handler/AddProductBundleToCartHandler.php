<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Handler;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Factory\ProductBundleOrderItemFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class AddProductBundleToCartHandler implements MessageHandlerInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;
    /** @var RepositoryInterface */
    private $productBundleRepository;
    /** @var FactoryInterface */
    private $orderItemFactory;
    /** @var ProductBundleOrderItemFactoryInterface */
    private $productBundleOrderItemFactory;
    /** @var OrderModifierInterface  */
    private $orderModifier;
    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;
    /** @var EntityManagerInterface  */
    private $orderEntityManager;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $productBundleRepository,
        FactoryInterface $orderItemFactory,
        ProductBundleOrderItemFactoryInterface $productBundleOrderItemFactory,
        OrderModifierInterface $orderModifier,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        EntityManagerInterface $orderEntityManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->productBundleRepository = $productBundleRepository;
        $this->orderItemFactory = $orderItemFactory;
        $this->productBundleOrderItemFactory = $productBundleOrderItemFactory;
        $this->orderModifier = $orderModifier;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderEntityManager = $orderEntityManager;
    }

    public function __invoke(AddProductBundleToCartCommand $addProductBundleToCartCommand): void
    {
        $cart = $this->getCart($addProductBundleToCartCommand);
        Assert::notNull($cart);

        /** @var ProductBundleInterface|null $productBundle */
        $productBundle = $this->productBundleRepository->find($addProductBundleToCartCommand->getProductBundleId());
        Assert::notNull($productBundle);

        $product = $productBundle->getProduct();
        Assert::notNull($product);

        /** @var ProductVariantInterface|false $productVariant */
        $productVariant = $product->getVariants()->first();
        Assert::notFalse($productVariant);

        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->orderItemFactory->createNew();
        $cartItem->setVariant($productVariant);

        $this->orderItemQuantityModifier->modify($cartItem, $addProductBundleToCartCommand->getQuantity());

        foreach ($productBundle->getProductBundleItems() as $bundleItem) {
            $productBundleOrderItem = $this->productBundleOrderItemFactory->createFromProductBundleItem($bundleItem);
            $cartItem->addProductBundleOrderItem($productBundleOrderItem);
        }

        $this->orderModifier->addToOrder($cart, $cartItem);
        $this->orderEntityManager->persist($cart);
        $this->orderEntityManager->flush();
    }

    private function getCart(AddProductBundleToCartCommand $addProductBundleToCartCommand): ?BaseOrderInterface
    {
        if (null !== $addProductBundleToCartCommand->getOrderToken()) {
            return $this->orderRepository->findCartByTokenValue($addProductBundleToCartCommand->getOrderToken());
        }

        return $this->orderRepository->findCartById($addProductBundleToCartCommand->getOrderId());
    }
}

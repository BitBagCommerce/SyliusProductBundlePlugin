<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Handler;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommand;
use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;

final class AddProductBundleToCartHandler implements MessageHandlerInterface
{
    /** @var FactoryInterface */
    private $productBundleOrderItemFactory;

    /** @var EntityManagerInterface */
    private $productBundleOrderItemEntityManager;

    /** @var OrderModifierInterface */
    private $orderModifier;

    /** @var EntityManagerInterface */
    private $orderEntityManager;

    public function __construct(
        FactoryInterface $productBundleOrderItemFactory,
        EntityManagerInterface $productBundleOrderItemEntityManager,
        OrderModifierInterface $orderModifier,
        EntityManagerInterface $orderEntityManager
    ) {
        $this->productBundleOrderItemFactory = $productBundleOrderItemFactory;
        $this->productBundleOrderItemEntityManager = $productBundleOrderItemEntityManager;
        $this->orderModifier = $orderModifier;
        $this->orderEntityManager = $orderEntityManager;
    }

    public function __invoke(AddProductBundleToCartCommand $addProductBundleToCartCommand): void
    {
        $cart = $addProductBundleToCartCommand->getCart();
        $cartItem = $addProductBundleToCartCommand->getCartItem();

        /** @var AddProductBundleItemToCartCommand $productBundleItem */
        foreach ($addProductBundleToCartCommand->getProductBundleItems() as $productBundleItem) {
            /** @var ProductBundleOrderItemInterface $productBundleOrderItem */
            $productBundleOrderItem = $this->productBundleOrderItemFactory->createNew();

            $productBundleOrderItem->setProductVariant($productBundleItem->getProductVariant());
            $productBundleOrderItem->setQuantity($productBundleItem->getQuantity());
            $productBundleOrderItem->setProductBundleItem($productBundleItem->getProductBundleItem());

            $cartItem->addProductBundleItem($productBundleOrderItem);
        }

        $this->orderModifier->addToOrder($cart, $cartItem);

        $this->orderEntityManager->persist($cart);
        $this->orderEntityManager->flush();
    }
}

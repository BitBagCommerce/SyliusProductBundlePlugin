<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Handler;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommand;
use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddProductBundleToCartHandler implements MessageHandlerInterface
{
    /** @var FactoryInterface */
    private $productBundleOrderItemFactory;

    /** @var OrderModifierInterface */
    private $orderModifier;

    /** @var EntityManagerInterface */
    private $orderEntityManager;

    public function __construct(
        FactoryInterface $productBundleOrderItemFactory,
        OrderModifierInterface $orderModifier,
        EntityManagerInterface $orderEntityManager
    ) {
        $this->productBundleOrderItemFactory = $productBundleOrderItemFactory;
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
            $cartItem->addProductBundleOrderItem($productBundleOrderItem);
        }

        $this->orderModifier->addToOrder($cart, $cartItem);
        $this->orderEntityManager->persist($cart);
        $this->orderEntityManager->flush();
    }
}

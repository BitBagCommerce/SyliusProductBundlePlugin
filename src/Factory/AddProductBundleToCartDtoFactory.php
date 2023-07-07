<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Factory;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommand;
use BitBag\SyliusProductBundlePlugin\Dto\AddProductBundleToCartDto;
use BitBag\SyliusProductBundlePlugin\Dto\AddProductBundleToCartDtoInterface;
use BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Order\Model\OrderInterface;

final class AddProductBundleToCartDtoFactory implements AddProductBundleToCartDtoFactoryInterface
{
    public function __construct(
        private AddProductBundleItemToCartCommandFactoryInterface $addProductBundleItemToCartCommandFactory
    ) {}

    public function createNew(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductInterface $product
    ): AddProductBundleToCartDtoInterface {
        /** @var ProductBundleInterface $productBundle */
        $productBundle = $product->getProductBundle();
        $processedProductBundleItems = $this->getProcessedProductBundleItems($productBundle);

        return new AddProductBundleToCartDto($order, $orderItem, $product, $processedProductBundleItems);
    }

    /**
     * @return AddProductBundleItemToCartCommand[]
     */
    private function getProcessedProductBundleItems(ProductBundleInterface $productBundle): array
    {
        $addProductBundleItemToCartCommands = [];

        foreach ($productBundle->getProductBundleItems() as $bundleItem) {
            $addProductBundleItemToCartCommands[] = $this->addProductBundleItemToCartCommandFactory->createNew($bundleItem);
        }

        return $addProductBundleItemToCartCommands;
    }
}

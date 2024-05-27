<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
        private AddProductBundleItemToCartCommandFactoryInterface $addProductBundleItemToCartCommandFactory,
    ) {
    }

    public function createNew(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductInterface $product,
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

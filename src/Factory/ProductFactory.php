<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Factory;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface as DecoratedProductFactoryInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductFactory implements ProductFactoryInterface
{
    public function __construct(
        private DecoratedProductFactoryInterface $decoratedFactory,
        private FactoryInterface $productBundleFactory,
    ) {
    }

    public function createWithVariantAndBundle(): BaseProductInterface
    {
        /** @var ProductBundleInterface $productBundle */
        $productBundle = $this->productBundleFactory->createNew();

        /** @var ProductInterface $product */
        $product = $this->createWithVariant();

        $productBundle->setProduct($product);

        $product->setProductBundle($productBundle);

        return $product;
    }

    public function createNew(): BaseProductInterface
    {
        /** @var BaseProductInterface $product */
        $product = $this->decoratedFactory->createNew();

        return $product;
    }

    public function createWithVariant(): BaseProductInterface
    {
        return $this->decoratedFactory->createWithVariant();
    }
}

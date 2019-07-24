<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Factory;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface as DecoratedProductFactoryInterface;

class ProductFactory implements ProductFactoryInterface
{
    /** @var DecoratedProductFactoryInterface */
    private $decoratedFactory;

    /** @var FactoryInterface */
    private $productBundleFactory;

    public function __construct(DecoratedProductFactoryInterface $decoratedFactory, FactoryInterface $productBundleFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->productBundleFactory = $productBundleFactory;
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

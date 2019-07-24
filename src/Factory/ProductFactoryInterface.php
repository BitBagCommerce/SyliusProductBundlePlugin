<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Factory;

use Sylius\Component\Product\Factory\ProductFactoryInterface as BaseProductFactoryInterface;
use Sylius\Component\Product\Model\ProductInterface;

interface ProductFactoryInterface extends BaseProductFactoryInterface
{
    public function createWithVariantAndBundle(): ProductInterface;
}

<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Entity;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundlesAwareTrait;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Core\Model\Product as BaseProduct;

class Product extends BaseProduct implements ProductInterface
{
    use ProductBundlesAwareTrait;
}

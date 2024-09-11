<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Command;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface AddProductBundleItemToCartCommandInterface
{
    public function getProductBundleItem(): ProductBundleItemInterface;

    public function getProductVariant(): ?ProductVariantInterface;

    public function setProductVariant(ProductVariantInterface $productVariant): void;

    public function getQuantity(): ?int;
}

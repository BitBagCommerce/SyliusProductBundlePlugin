<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ProductBundleInterface extends ResourceInterface, TimestampableInterface
{
    public function getProduct(): ?ProductInterface;

    public function setProduct(?ProductInterface $product): void;

    public function getProductBundleItems(): Collection;

    public function addProductBundleItem(ProductBundleItemInterface $productBundleItem): void;

    public function removeProductBundleItem(ProductBundleItemInterface $productBundleItem): void;

    public function hasProductBundleItem(ProductBundleItemInterface $productBundleItem): bool;

    public function isPackedProduct(): bool;

    public function setIsPackedProduct(bool $isPackedProduct): void;
}

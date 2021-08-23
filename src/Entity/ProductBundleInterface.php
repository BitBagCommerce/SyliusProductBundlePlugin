<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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

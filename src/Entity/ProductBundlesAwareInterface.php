<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Entity;

interface ProductBundlesAwareInterface
{
    public function getProductBundle(): ?ProductBundleInterface;

    public function setProductBundle(?ProductBundleInterface $productBundle): void;

    public function isBundle(): bool;
}

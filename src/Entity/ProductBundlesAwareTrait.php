<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Entity;

trait ProductBundlesAwareTrait
{
    /** @var ProductBundleInterface */
    protected $productBundle;

    public function getProductBundle(): ?ProductBundleInterface
    {
        return $this->productBundle;
    }

    public function setProductBundle(?ProductBundleInterface $productBundle): void
    {
        $this->productBundle = $productBundle;
    }

    public function isBundle(): bool
    {
        return null !== $this->getProductBundle();
    }
}

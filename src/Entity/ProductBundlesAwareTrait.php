<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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

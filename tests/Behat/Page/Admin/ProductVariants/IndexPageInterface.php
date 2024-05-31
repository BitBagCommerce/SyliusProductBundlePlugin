<?php

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Page\Admin\ProductVariants;

use Sylius\Component\Core\Model\ProductVariantInterface;

interface IndexPageInterface
{
    public function getOnHoldQuantityFor(ProductVariantInterface $productVariant): int;

    public function getOnHandQuantityFor(ProductVariantInterface $productVariant): int;
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Twig\Extension;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\Repository\ProductBundleItemRepositoryInterface;
use BitBag\SyliusProductBundlePlugin\Repository\ProductBundleRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductBundlesExtension extends AbstractExtension
{
    public function __construct(
        private readonly ProductBundleRepositoryInterface $productBundleRepository,
        private readonly ProductBundleItemRepositoryInterface $productBundleItemRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('bitbag_get_bundles_containing_product', [$this, 'getBundlesForProduct'], ['is_safe' => ['html']]),
            new TwigFunction('bitbag_get_products_from_bundle', [$this, 'getProductsFromBundle'], ['is_safe' => ['html']]),
        ];
    }

    /** @return ProductBundleInterface[] */
    public function getBundlesForProduct(ProductInterface $product): array
    {
        return $this->productBundleRepository->findBundlesByVariants($product->getVariants());
    }

    /** @return ProductBundleItemInterface[] */
    public function getProductsFromBundle(ProductInterface $product): array
    {
        return $this->productBundleItemRepository->findByBundleCode((string) $product->getCode());
    }
}

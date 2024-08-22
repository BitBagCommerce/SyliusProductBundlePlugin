<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Fixture\Factory;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductBundleFixtureFactory implements ExampleFactoryInterface
{
    private readonly OptionsResolver $optionsResolver;

    public function __construct(
        private readonly FactoryInterface $productBundleFactory,
        private readonly FactoryInterface $productBundleItemFactory,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductVariantRepositoryInterface $productVariantRepository,
    ) {
        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
    }

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('bundle', '')
            ->setAllowedTypes('bundle', 'string')
            ->setDefault('items', [])
            ->setAllowedTypes('items', 'array')
            ->setDefault('is_packed', '')
            ->setAllowedTypes('is_packed', 'bool')
        ;
    }

    public function create(array $options = []): ProductBundleInterface
    {
        $options = $this->optionsResolver->resolve($options);

        $bundleProduct = $this->productRepository->findOneByCode($options['bundle']);
        /** @var ProductBundleInterface $productBundle */
        $productBundle = $this->productBundleFactory->createNew();
        $productBundle->setProduct($bundleProduct);
        $productBundle->setIsPackedProduct($options['is_packed']);

        foreach ($options['items'] ?? [] as $item) {
            /** @var ProductVariantInterface $productVariant */
            $productVariant = $this->productVariantRepository->findOneBy(['code' => $item]);
            /** @var ProductBundleItemInterface $bundleItem */
            $bundleItem = $this->productBundleItemFactory->createNew();
            $bundleItem->setProductVariant($productVariant);
            $bundleItem->setQuantity(1);
            $bundleItem->setProductBundle($productBundle);
            $productBundle->addProductBundleItem($bundleItem);
        }

        return $productBundle;
    }
}

<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItem;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\Factory\ProductFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductBundleContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private FactoryInterface $taxonFactory,
        private ProductRepositoryInterface $productRepository,
        private FactoryInterface $productTaxonFactory,
        private EntityManagerInterface $productTaxonManager,
        private ProductFactory $productFactory,
        private FactoryInterface $productBundleItemFactory,
        private FactoryInterface $channelPricingFactory,
        private ProductVariantResolverInterface $productVariantResolver,
        private SlugGeneratorInterface $slugGenerator
    ) {
    }

    /**
     * @Given /^the store has bundled product "([^"]*)" priced at ("[^"]+") which contains "([^"]*)" and "([^"]*)"$/
     */
    public function theStoreHasBundledProductPricedAtWhichContainsAnd(
        string $productBundleName,
        int $productBundlePrice,
        string $firstProductName,
        string $secondProductName
    ): void {
        $product = $this->createProduct($productBundleName, $firstProductName, $secondProductName, $productBundlePrice);
        $this->saveProduct($product);
    }

    /**
     * @Given /^all store products appear under a main taxonomy$/
     */
    public function allStoreProductsAppearUnderAMainTaxonomy(): void
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setCode('main');
        $taxon->setSlug('main');
        $taxon->setName('Main');
        $this->productTaxonManager->persist($taxon);

        /** @var ProductInterface $product */
        foreach ($this->productRepository->findAll() as $product) {
            /** @var ProductTaxonInterface $productTaxon */
            $productTaxon = $this->productTaxonFactory->createNew();
            $productTaxon->setTaxon($taxon);
            $productTaxon->setProduct($product);
            $product->addProductTaxon($productTaxon);
            $this->productTaxonManager->persist($productTaxon);
        }

        $this->productTaxonManager->flush();
    }

    private function createChannelPricingForChannel(int $price, ChannelInterface $channel = null): ChannelPricingInterface
    {
        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setPrice($price);
        $channelPricing->setChannelCode($channel->getCode());

        return $channelPricing;
    }

    private function saveProduct(ProductInterface $product): void
    {
        $this->productRepository->add($product);
        $this->sharedStorage->set('product_with_bundle_item', $product);
    }

    private function createProduct(
        string $productBundleName,
        string $firstProductName,
        string $secondProductName,
        int $productBundlePrice
    ): ProductInterface {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createWithVariantAndBundle();
        $channel = $this->sharedStorage->get('channel');
        $product->setCode(StringInflector::nameToUppercaseCode($productBundleName));
        $product->setName($productBundleName);
        $product->setSlug($this->slugGenerator->generate($productBundleName));
        if (null !== $channel) {
            $product->addChannel($channel);
            foreach ($channel->getLocales() as $locale) {
                $product->setFallbackLocale($locale->getCode());
                $product->setCurrentLocale($locale->getCode());
                $product->setName($productBundleName);
                $product->setSlug($this->slugGenerator->generate($productBundleName));
            }
        }
        $productBundle = $product->getProductBundle();
        /** @var ProductInterface $firstProduct */
        $firstProduct = $this->productRepository->findByName($firstProductName, 'en_US');
        /** @var ProductInterface $secondProduct */
        $secondProduct = $this->productRepository->findByName($secondProductName, 'en_US');
        /** @var ProductBundleItemInterface $firstProductBundleItem */
        $firstProductBundleItem = $this->productBundleItemFactory->createNew();
        $firstProductBundleItem->setQuantity(1);
        /** @var ProductVariantInterface|null $firstProductVariant */
        $firstProductVariant = $this->productVariantResolver->getVariant($firstProduct[0]);
        $firstProductBundleItem->setProductVariant($firstProductVariant);
        $productBundle->addProductBundleItem($firstProductBundleItem);
        /** @var ProductBundleItemInterface $secondProductBundleItem */
        $secondProductBundleItem = $this->productBundleItemFactory->createNew();
        $secondProductBundleItem->setQuantity(1);
        /** @var ProductVariantInterface|null $secondProductVariant */
        $secondProductVariant = $this->productVariantResolver->getVariant($secondProduct[0]);
        $secondProductBundleItem->setProductVariant($secondProductVariant);
        $productBundle->addProductBundleItem($secondProductBundleItem);
        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->productVariantResolver->getVariant($product);
        if (null !== $channel) {
            $productVariant->addChannelPricing($this->createChannelPricingForChannel($productBundlePrice, $channel));
        }

        $productVariant->setCode($product->getCode());
        $productVariant->setName($product->getName());

        return $product;
    }

    /**
     * @Given update bundled products stock environmental variable is set to :value
     */
    public function updateBundledProductsStockEnvironmentalVariableIsSetTo(string $envValue)
    {
        $_ENV['APP_UPDATE_BUNDLED_PRODUCTS_STOCK'] = (int) $envValue;
    }
}

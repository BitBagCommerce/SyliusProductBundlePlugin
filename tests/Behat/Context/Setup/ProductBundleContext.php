<?php

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Factory\ProductFactory;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;

final class ProductBundleContext implements Context
{


    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;
    /**
     * @var FactoryInterface
     */
    private $taxonFactory;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var FactoryInterface
     */
    private $productTaxonFactory;
    /**
     * @var EntityManagerInterface
     */
    private $productTaxonManager;
    /**
     * @var ProductFactory
     */
    private $productFactory;
    /**
     * @var FactoryInterface
     */
    private $productBundleItemFactory;
    /**
     * @var FactoryInterface
     */
    private $channelPricingFactory;
    /**
     * @var ProductVariantResolverInterface
     */
    private $productVariantResolver;
    /**
     * @var SlugGeneratorInterface
     */
    private $slugGenerator;
    /**
     * @var FactoryInterface
     */
    private $orderFactory;
    /**
     * @var FactoryInterface
     */
    private $orderItemFactory;
    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $orderItemQuantityModifier;
    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessor;
    /**
     * @var RepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $taxonFactory,
        ProductRepositoryInterface $productRepository,
        FactoryInterface $productTaxonFactory,
        EntityManagerInterface $productTaxonManager,
        ProductFactory $productFactory,
        FactoryInterface $productBundleItemFactory,
        FactoryInterface $channelPricingFactory,
        ProductVariantResolverInterface $productVariantResolver,
        SlugGeneratorInterface $slugGenerator,
        FactoryInterface $orderFactory,
        FactoryInterface $orderItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
        RepositoryInterface $orderRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->taxonFactory = $taxonFactory;
        $this->productRepository = $productRepository;
        $this->productTaxonFactory = $productTaxonFactory;
        $this->productTaxonManager = $productTaxonManager;
        $this->productFactory = $productFactory;
        $this->productBundleItemFactory = $productBundleItemFactory;
        $this->channelPricingFactory = $channelPricingFactory;
        $this->productVariantResolver = $productVariantResolver;
        $this->slugGenerator = $slugGenerator;
        $this->orderFactory = $orderFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderProcessor = $orderProcessor;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Given /^the store has bundled product "([^"]*)" priced at ("[^"]+") which contains "([^"]*)" and "([^"]*)"$/
     */
    public function theStoreHasBundledProductPricedAtWhichContainsAnd(string $productBundleName, int $productBundlePrice, string $firstProductName, string $secondProductName)
    {
        $product = $this->createProduct($productBundleName, $firstProductName, $secondProductName, $productBundlePrice);

        $this->saveProduct($product);
    }

    /**
     * @return ChannelPricingInterface
     */
    private function createChannelPricingForChannel(int $price, ChannelInterface $channel = null)
    {
        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setPrice($price);
        $channelPricing->setChannelCode($channel->getCode());

        return $channelPricing;
    }

    private function saveProduct(ProductInterface $product)
    {
        $this->productRepository->add($product);
        $this->sharedStorage->set('product_with_bundle_item', $product);
    }

    /**
     * @Given /^all store products appear under a main taxonomy$/
     */
    public function allStoreProductsAppearUnderAMainTaxonomy()
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

    /**
     * @When /^I add bundled product to the cart$/
     */
    public function iAddBundledProductToTheCart()
    {
        /** @var OrderInterface $order */
        $order = $this->orderFactory->createNew();
        /** @var ChannelInterface $channel */
        $channel = $this->sharedStorage->get('channel');
        /** @var LocaleInterface $locale */
        $locale = $this->sharedStorage->get('locale');

        /** @var ShopUserInterface $user */
        $user = $this->sharedStorage->get('user');

        $order->setChannel($channel);
        $order->setLocaleCode($locale->getCode());
        $order->setCurrencyCode($channel->getBaseCurrency()->getCode());
        $order->setCustomer($user->getCustomer());

        $product = $this->sharedStorage->get('product_with_bundle_item');



    }


    /**
     * @Given /^I should have one item in cart$/
     */
    public function iShouldHaveOneItemInCart()
    {
        throw new PendingException();
    }

    /**
     * @param $productBundleName
     * @param $firstProductName
     * @param $secondProductName
     * @param $productBundlePrice
     * @return \BitBag\SyliusProductBundlePlugin\Entity\ProductInterface
     */
    private function createProduct(string $productBundleName,  string $firstProductName,  string $secondProductName,  int $productBundlePrice): \BitBag\SyliusProductBundlePlugin\Entity\ProductInterface
    {
        /** @var \BitBag\SyliusProductBundlePlugin\Entity\ProductInterface $product */
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
        /** @var \BitBag\SyliusProductBundlePlugin\Entity\ProductInterface $firstProduct */
        $firstProduct = $this->productRepository->findByName($firstProductName, 'en_US');
        /** @var \BitBag\SyliusProductBundlePlugin\Entity\ProductInterface $secondProduct */
        $secondProduct = $this->productRepository->findByName($secondProductName, 'en_US');
        /** @var ProductBundleItemInterface $firstProductBundleItem */
        $firstProductBundleItem = $this->productBundleItemFactory->createNew();
        $firstProductBundleItem->setQuantity(1);
        $firstProductVariant = $this->productVariantResolver->getVariant($firstProduct[0]);
        $firstProductBundleItem->setProductVariant($firstProductVariant);
        $productBundle->addProductBundleItem($firstProductBundleItem);
        /** @var ProductBundleItemInterface $secondProductBundleItem */
        $secondProductBundleItem = $this->productBundleItemFactory->createNew();
        $secondProductBundleItem->setQuantity(1);
        $secondProductVariant = $this->productVariantResolver->getVariant($secondProduct[0]);
        $secondProductBundleItem->setProductVariant($secondProductVariant);
        $productBundle->addProductBundleItem($secondProductBundleItem);
        $productVariant = $this->productVariantResolver->getVariant($product);
        if (null !== $channel) {
            $productVariant->addChannelPricing($this->createChannelPricingForChannel($productBundlePrice, $channel));
        }

        $productVariant->setCode($product->getCode());
        $productVariant->setName($product->getName());

        return $product;
    }
}

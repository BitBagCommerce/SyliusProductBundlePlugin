<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Tests\BitBag\SyliusProductBundlePlugin\Behat\Page\Admin\CreateBundledProductPageInterface;
use Tests\BitBag\SyliusProductBundlePlugin\Behat\Page\Shop\BundledProductsListPageInterface;
use Webmozart\Assert\Assert;

class ProductBundleContext implements Context
{
    public function __construct(
        private CreateBundledProductPageInterface $createBundledProductPage,
        private ProductRepositoryInterface $productRepository,
        private BundledProductsListPageInterface $summaryPage,
        private BundledProductsListPageInterface $orderShowPage,
    ) {
    }

    /**
     * @When I want to create a new bundled product
     */
    public function iWantToCreateANewBundledProduct(): void
    {
        $this->createBundledProductPage->open();
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCode(string $code): void
    {
        $this->createBundledProductPage->specifyCode($code);
    }

    /**
     * @When I name it :name in :language
     */
    public function iRenameItToIn(?string $name = null, ?string $language = null): void
    {
        if (null !== $name && null !== $language) {
            $this->createBundledProductPage->nameItIn($name, $language);
        }
    }

    /**
     * @When I set its slug to :slug
     * @When I set its slug to :slug in :language
     */
    public function iSetItsSlugToIn(?string $slug = null, $language = 'en_US')
    {
        $this->createBundledProductPage->specifySlugIn($slug, $language);
    }

    /**
     * @When /^I set its(?:| default) price to "(?:€|£|\$)([^"]+)" for ("([^"]+)" channel)$/
     */
    public function iSetItsPriceTo(string $price, ChannelInterface $channel)
    {
        $this->createBundledProductPage->specifyPrice($channel, $price);
    }

    /**
     * @When /^I set its original price to "(?:€|£|\$)([^"]+)" for ("([^"]+)" channel)$/
     */
    public function iSetItsOriginalPriceTo(int $originalPrice, ChannelInterface $channel)
    {
        $this->createBundledProductPage->specifyOriginalPrice($channel, $originalPrice);
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->createBundledProductPage->create();
    }

    /**
     * @When I add product :productName to the bundle
     * @When I add product :firstProductName and :secondProductName to the bundle
     * @When I add product :firstProductName and :secondProductName and :thirdProductName to the bundle
     */
    public function iAddProductsToBundledProduct(...$productsNames)
    {
        $this->createBundledProductPage->addProductsToBundle($productsNames);
    }

    /**
     * @When I add product :firstProductName with quantity :firstQuantity and :secondProductName with quantity :secondQuantity to the bundle
     */
    public function iAddProductsWithQuantitiesToBundledProduct(string $firstProductName, string $secondProductName, int $firstQuantity, int $secondQuantity)
    {
        $this->createBundledProductPage->addProductsToBundle([$firstProductName, $secondProductName], [$firstQuantity, $secondQuantity]);
    }

    /**
     * @When there should be a :bundleName bundle containing :productName with quantity :quantity
     */
    public function theProductBundleShouldContainProductWithQuantity(string $bundleCode, string $productName, int $quantity): void
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy(['code' => $bundleCode]);

        Assert::notNull($product->getProductBundle());

        $bundleItems = $product->getProductBundle()->getProductBundleItems();
        /** @var ProductBundleItemInterface $item */
        foreach ($bundleItems as $item) {
            if ($item->getProductVariant()->getProduct()->getName() === $productName) {
                Assert::same($item->getQuantity(), $quantity);

                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('Product "%s" not found in bundle "%s"', $productName, $bundleCode));
    }

    /**
     * @When there should be bundled products listed
     */
    public function thereShouldBeBundledProductsListed(): void
    {
        Assert::true($this->summaryPage->hasBundledProductsList());
    }

    /**
     * @When the list should contain :productName
     */
    public function thereShouldBeAProductOnTheList(string $productName): void
    {
        Assert::true($this->summaryPage->hasBundledProduct($productName));
    }

    /**
     * @When there should be bundled products listed in order details
     */
    public function thereShouldBeBundledProductsListedOnOrderShowPage(): void
    {
        Assert::true($this->orderShowPage->hasBundledProductsList());
    }

    /**
     * @When the list should contain :productName in order details
     */
    public function thereShouldBeAProductOnTheListOnOrderShowPage(string $productName): void
    {
        Assert::true($this->orderShowPage->hasBundledProduct($productName));
    }
}

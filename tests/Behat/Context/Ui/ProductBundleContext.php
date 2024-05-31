<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Tests\BitBag\SyliusProductBundlePlugin\Behat\Page\Admin\CreateBundledProductPageInterface;
use Tests\BitBag\SyliusProductBundlePlugin\Behat\Page\Admin\ProductVariants\IndexPageInterface;
use Webmozart\Assert\Assert;

final class ProductBundleContext implements Context
{
    public function __construct(
        private CreateBundledProductPageInterface $createBundledProductPage,
        private IndexPageInterface $indexPage,
        private ProductVariantResolverInterface $productVariantResolver,
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
     * @Then /^(\d+) units of the (product "[^"]+") should be on hold$/
     */
    public function unitsOfProductShouldBeOnHold(string $units, ProductInterface $product)
    {
        $this->assertOnHoldQuantityOfProduct($units, $product);
    }

    /**
     * @Then /^(\d+) units of the (product "[^"]+") should be on hand$/
     */
    public function unitsOfProductShouldBeOnHand(string $units, ProductInterface $product)
    {
        $this->assertOnHandQuantityOfProduct($units, $product);
    }

    private function assertOnHoldQuantityOfProduct(string $expectedAmount, ProductInterface $product): void
    {
        $productVariant = $this->productVariantResolver->getVariant($product);
        $actualAmount = $this->indexPage->getOnHoldQuantityFor($productVariant);

        Assert::same(
            $actualAmount,
            (int) $expectedAmount,
            sprintf(
                'Unexpected on hold quantity for "%s" variant. It should be "%s" but is "%s"',
                $product->getName(),
                $expectedAmount,
                $actualAmount,
            ),
        );
    }

    private function assertOnHandQuantityOfProduct(string $expectedAmount, ProductInterface $product): void
    {
        $productVariant = $this->productVariantResolver->getVariant($product);
        $actualAmount = $this->indexPage->getOnHandQuantityFor($productVariant);

        Assert::same(
            $actualAmount,
            (int) $expectedAmount,
            sprintf(
                'Unexpected on hand quantity for "%s" variant. It should be "%s" but is "%s"',
                $product->getName(),
                $expectedAmount,
                $actualAmount,
            ),
        );
    }
}

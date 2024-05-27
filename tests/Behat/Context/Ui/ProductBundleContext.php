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
use Sylius\Component\Core\Model\ChannelInterface;
use Tests\BitBag\SyliusProductBundlePlugin\Behat\Page\Admin\CreateBundledProductPageInterface;

class ProductBundleContext implements Context
{
    public function __construct(
        private CreateBundledProductPageInterface $createBundledProductPage,
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
}

<?php

/*
 * This file is part of the Sylius ProductBundle Plugin package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\ProductBundlePlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Sylius\Component\Core\Model\ChannelInterface;
use Tests\Sylius\ProductBundlePlugin\Behat\Page\Admin\CreateBundledProductPageInterface;

readonly class ProductBundleContext implements Context
{
    public function __construct(
        private CreateBundledProductPageInterface $createBundledProductPage,
    ) {
    }

    #[When('I want to create a new bundled product')]
    public function iWantToCreateANewBundledProduct(): void
    {
        $this->createBundledProductPage->open();
    }

    #[When('I specify its code as :code')]
    public function iSpecifyItsCode(string $code): void
    {
        $this->createBundledProductPage->specifyCode($code);
    }

    #[When('I name it :name in :language')]
    public function iRenameItToIn(?string $name = null, ?string $language = null): void
    {
        if (null !== $name && null !== $language) {
            $this->createBundledProductPage->nameItIn($name, $language);
        }
    }

    #[When('I set its slug to :slug')]
    #[When('I set its slug to :slug in :language')]
    public function iSetItsSlugToIn(?string $slug = null, $language = 'en_US'): void
    {
        $this->createBundledProductPage->specifySlugIn($slug, $language);
    }

    #[When('/^I set its(?:| default) price to "(?:€|£|\$)([^"]+)" for ("([^"]+)" channel)$/')]
    public function iSetItsPriceTo(string $price, ChannelInterface $channel): void
    {
        $this->createBundledProductPage->specifyPrice($channel, $price);
    }

    #[When('/^I set its original price to "(?:€|£|\$)([^"]+)" for ("([^"]+)" channel)$/')]
    public function iSetItsOriginalPriceTo(int $originalPrice, ChannelInterface $channel): void
    {
        $this->createBundledProductPage->specifyOriginalPrice($channel, $originalPrice);
    }

    #[When('I add it')]
    public function iAddIt()
    {
        $this->createBundledProductPage->create();
    }

    #[When('I add product :productName to the bundle')]
    #[When('I add product :firstProductName and :secondProductName to the bundle')]
    #[When('I add product :firstProductName and :secondProductName and :thirdProductName to the bundle')]
    public function iAddProductsToBundledProduct(string ...$productsNames): void
    {
        $this->createBundledProductPage->addProductsToBundle($productsNames);
    }

    #[Then('I should be notified that bundle must contain at least two products')]
    public function iShouldBeNotifiedThatBundleMustContainAtLeaseTwoProducts(): void
    {
        $this->createBundledProductPage->getProductBundleValidationErrors();
    }
}

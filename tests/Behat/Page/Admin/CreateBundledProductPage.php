<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Page\Admin;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\CreatePage;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\SlugGenerationHelper;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

class CreateBundledProductPage extends CreatePage implements CreateBundledProductPageInterface
{
    public function specifyCode(string $code): void
    {
        $this->getDocument()->fillField('Code', $code);
    }

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->clickTabIfItsNotActive('details');
        $this->activateLanguageTab($localeCode);
        $this->getElement('name', ['%locale%' => $localeCode])->setValue($name);

//        if (DriverHelper::isJavascript($this->getDriver())) {
//            SlugGenerationHelper::waitForSlugGeneration(
//                $this->getSession(),
//                $this->getElement('slug', ['%locale%' => $localeCode]),
//            );
//        }
    }

    public function specifySlugIn(?string $slug, string $locale): void
    {
        $this->activateLanguageTab($locale);

        $this->getElement('slug', ['%locale%' => $locale])->setValue($slug);
    }

    public function activateLanguageTab(string $locale): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $languageTabTitle = $this->getElement('language_tab', ['%locale%' => $locale]);
        if (!$languageTabTitle->hasClass('active')) {
            $languageTabTitle->click();
        }
    }

    public function specifyPrice(ChannelInterface $channel, string $price): void
    {
        $this->getElement('price', ['%channelCode%' => $channel->getCode()])->setValue($price);
    }

    public function specifyOriginalPrice(ChannelInterface $channel, int $originalPrice): void
    {
        $this->getElement('original_price', ['%channelCode%' => $channel->getCode()])->setValue($originalPrice);
    }

    public function addProductsToBundle(array $productsNames): void
    {
        $this->clickTabIfItsNotActive('bundle');

        $productCounter = 0;

        foreach ($productsNames as $productName) {
            $addSelector = $this->getElement('add_product_to_bundle_button');
            $addSelector->click();
            $addSelector->waitFor(5, fn () => $this->hasElement('product_selector_dropdown'));

            $dropdown = $this->getLastImageElement();
            $dropdown->click();
            $dropdown->waitFor(5, fn () => $this->hasElement('product_selector_dropdown_item'));

            $item = $this->getElement('product_selector_dropdown_item', [
                '%item%' => $productName,
            ]);
            $item->click();

            $this->getElement('product_selector_quantity', ['%productCounter%' => $productCounter])->setValue('1');

            ++$productCounter;
        }
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'product_selector_quantity' => '#sylius_product_productBundle_productBundleItems_%productCounter%_quantity',
            'product_selector_dropdown_item' => '#add_product_to_bundle_autocomplete > div > div > div.menu.transition.visible > div.item:contains("%item%")',
            'product_selector_dropdown' => '#add_product_to_bundle_autocomplete',
            'add_product_to_bundle_button' => '#bitbag_add_product_to_bundle_button',
            'code' => '#sylius_product_code',
            'language_tab' => '[data-locale="%locale%"] .title',
            'name' => '#sylius_product_translations_%locale%_name',
            'original_price' => '#sylius_product_variant_channelPricings_%channelCode%_originalPrice',
            'price' => '#sylius_product_variant_channelPricings_%channelCode%_price',
            'slug' => '#sylius_product_translations_%locale%_slug',
            'tab' => '.menu [data-tab="%name%"]',
        ]);
    }

    private function clickTabIfItsNotActive(string $tabName): void
    {
        $attributesTab = $this->getElement('tab', ['%name%' => $tabName]);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }
    }

    private function getLastImageElement(): NodeElement
    {
        $items = $this->getDocument()->findAll('css', '#add_product_to_bundle_autocomplete');

        Assert::notEmpty($items);

        return end($items);
    }
}

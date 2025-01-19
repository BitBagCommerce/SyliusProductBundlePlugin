<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Page\Admin;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Context\Ui\Admin\Helper\NavigationTrait;
use Sylius\Behat\Page\Admin\Crud\CreatePage;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

class CreateBundledProductPage extends CreatePage implements CreateBundledProductPageInterface
{
    use NavigationTrait;

    public function specifyCode(string $code): void
    {
        $this->getDocument()->fillField('Code', $code);
    }

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->clickTabIfItsNotActive('translations');
        $this->activateLanguageTab($localeCode);
        $this->getElement('name', ['%locale%' => $localeCode])->setValue($name);
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
        $this->clickTabIfItsNotActive('channel-pricing');
        $this->getElement('price', ['%channelCode%' => $channel->getCode()])->setValue($price);
    }

    public function specifyOriginalPrice(ChannelInterface $channel, int $originalPrice): void
    {
        $this->clickTabIfItsNotActive('channel-pricing');
        $this->getElement('original_price', ['%channelCode%' => $channel->getCode()])->setValue($originalPrice);
    }

    public function addProductsToBundle(array $productsNames): void
    {
        $this->clickTabIfItsNotActive('bundle');

        $productCounter = 0;

        foreach ($productsNames as $productName) {
            if (DriverHelper::isNotJavascript($this->getDriver())) {
                return;
            }

            $addSelector = $this->getElement('add_product_to_bundle_button');
            $addSelector->click();
            $addSelector->waitFor(5, fn () => $this->hasElement('product_selector_dropdown'));

            $dropdown = $this->getLastProductAutocomplete();
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
            'add_product_to_bundle_button' => '#sylius_admin_product_productBundle_productBundleItems_add',
            'code' => '#sylius_product_code',
            'language_tab' => '[data-locale="%locale%"] .title',
            'name' => '#sylius_admin_product_translations_%locale%_name',
            'original_price' => '#sylius_admin_product_variant_channelPricings_%channelCode%_originalPrice',
            'price' => '#sylius_admin_product_variant_channelPricings_%channelCode%_price',
            'product_selector_quantity' => '#sylius_product_productBundle_productBundleItems_%productCounter%_quantity',
            'product_selector_dropdown_item' => '#add_product_to_bundle_autocomplete > div > div > div.menu.transition.visible > div.item:contains("%item%")',
            'product_selector_dropdown' => '#add_product_to_bundle_autocomplete',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
            'slug' => '#sylius_admin_product_translations_%locale%_slug',
        ]);
    }

    private function clickTabIfItsNotActive(string $tabName): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $attributesTab = $this->getElement('side-navigation-tab', ['%name%' => $tabName]);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }
    }

    private function getLastProductAutocomplete(): NodeElement
    {
        $items = $this->getDocument()->findAll('css', '#add_product_to_bundle_autocomplete');

        Assert::notEmpty($items);

        return end($items);
    }

    public function getResourceName(): string
    {
        return 'product';
    }
}

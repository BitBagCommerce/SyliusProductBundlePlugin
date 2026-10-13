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

namespace Tests\Sylius\ProductBundlePlugin\Behat\Page\Admin;

use Behat\Mink\Session;
use Sylius\Behat\Context\Ui\Admin\Helper\NavigationTrait;
use Sylius\Behat\Page\Admin\Crud\CreatePage;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class CreateBundledProductPage extends CreatePage implements CreateBundledProductPageInterface
{
    use NavigationTrait;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName,
        protected readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $routeName);
    }

    public function specifyCode(string $code): void
    {
        $this->getDocument()->fillField('Code', $code);
    }

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->clickTabIfItsNotActive('translations');
        $this->getElement('name', ['%locale%' => $localeCode])->setValue($name);
    }

    public function specifySlugIn(?string $slug, string $locale): void
    {
        $this->getElement('slug', ['%locale%' => $locale])->setValue($slug);
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
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->clickTabIfItsNotActive('bundle');

        foreach ($productsNames as $productCounter => $productName) {
            $addSelector = $this->getElement('add_product_to_bundle_button');
            $addSelector->click();
            $addSelector->waitFor(5, fn () => $this->hasElement('bundle_item', ['%item%' => $productCounter]));
            $bundleItem = $this->getElement('bundle_item', ['%item%' => $productCounter]);

            $this->autocompleteHelper->selectByName(
                $this->getDriver(),
                $bundleItem->find('css', 'select')->getXpath(),
                $productName,
            );

            $bundleItem->find('css', 'input[type="number"]')->setValue('1');
        }
    }

    public function getProductBundleValidationErrors(): void
    {
        Assert::same(
            $this->getElement('product_bundle_validation_error')->getText(),
            'At least two products must be added to the bundle.',
        );
    }

    public function getResourceName(): string
    {
        return 'product';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_product_to_bundle_button' => '#sylius_admin_product_productBundle_productBundleItems_add',
            'bundle_item' => '[data-test-bundle-item="%item%"]',
            'bundle_item_product_variant' => '[data-test-bundle-item-product-variant]',
            'bundle_item_quantity' => '[data-test-bundle-item-quantity]',
            'code' => '#sylius_product_code',
            'name' => '#sylius_admin_product_translations_%locale%_name',
            'original_price' => '#sylius_admin_product_variant_channelPricings_%channelCode%_originalPrice',
            'price' => '#sylius_admin_product_variant_channelPricings_%channelCode%_price',
            'product_bundle_validation_error' => '#sylius_admin_product .alert-danger',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
            'slug' => '#sylius_admin_product_translations_%locale%_slug',
        ]);
    }

    private function clickTabIfItsNotActive(string $tabName): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $attributesTab = $this->getElement('side_navigation_tab', ['%name%' => $tabName]);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
            // Long wait for tab animation and content to render
            $this->getSession()->wait(5000);
        }
    }
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Page\Shop;

use Sylius\Behat\Page\Shop\Account\Order\ShowPage as BaseOrderShowPage;

class OrderShowPage extends BaseOrderShowPage implements BundledProductsListPageInterface
{
    public function hasBundledProductsList(): bool
    {
        return $this->hasElement('products_in_bundle');
    }

    public function hasBundledProduct(string $productName): bool
    {
        return $this->hasElement('bundled_product', ['%productName%' => $productName]);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'products_in_bundle' => '#sylius-order > tbody > tr:nth-child(2) > td > div > div.title.bundled-items-header > strong:contains("Products in bundle")',
            'bundled_product' => '#sylius-order > tbody > tr:nth-child(2) > td > div > div.content.bundled-items > div > div > div > div:contains("%productName%")',
        ]);
    }
}

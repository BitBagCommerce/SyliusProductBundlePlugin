<?php

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Page\Admin;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Product\Model\ProductInterface;

interface CreateBundledProductPageInterface extends CreatePageInterface
{
    public function specifyCode(string $code): void;

    public function nameItIn(string $name, string $localeCode): void;

    public function specifySlugIn(?string $slug, string $locale): void;

    public function specifyPrice(ChannelInterface $channel, string $price): void;

    public function specifyOriginalPrice(ChannelInterface $channel, int $originalPrice): void;

    public function addProductsToBundle(array $productsNames): void;
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Behat\Page\Admin;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface CreateBundledProductPageInterface extends CreatePageInterface
{
    public function specifyCode(string $code): void;

    public function nameItIn(string $name, string $localeCode): void;

    public function specifySlugIn(?string $slug, string $locale): void;

    public function specifyPrice(ChannelInterface $channel, string $price): void;

    public function specifyOriginalPrice(ChannelInterface $channel, int $originalPrice): void;

    public function addProductsToBundle(array $productsNames): void;
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Menu;

use Sylius\Bundle\AdminBundle\Event\ProductMenuBuilderEvent;

final class AdminProductFormMenuListener
{
    public function addItems(ProductMenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $menu
            ->addChild('bundle')
            ->setAttribute('template', '@BitBagSyliusProductBundlePlugin/Admin/product/form/side_navigation/bundle.html.twig')
            ->setLabel('bitbag_sylius_product_bundle.ui.bundle')
        ;
    }
}

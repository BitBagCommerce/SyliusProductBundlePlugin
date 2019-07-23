<?php

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
            ->setAttribute('template', '@BitBagSyliusProductBundlePlugin/Admin/Product/Tab/_bundle.html.twig')
            ->setLabel('bitbag_sylius_product_bundle.ui.bundle')
        ;
    }
}

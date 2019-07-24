<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Menu;

use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use Sylius\Bundle\AdminBundle\Event\ProductMenuBuilderEvent;

final class AdminProductFormMenuListener
{
    public function addItems(ProductMenuBuilderEvent $event): void
    {
        /** @var ProductInterface $product */
        $product = $event->getProduct();

        if (!$product->isBundle()) {
            return;
        }

        $menu = $event->getMenu();

        $menu
            ->addChild('bundle')
            ->setAttribute('template', '@BitBagSyliusProductBundlePlugin/Admin/Product/Tab/_bundle.html.twig')
            ->setLabel('bitbag_sylius_product_bundle.ui.bundle')
        ;
    }
}

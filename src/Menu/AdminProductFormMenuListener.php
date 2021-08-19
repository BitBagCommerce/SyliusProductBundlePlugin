<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

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

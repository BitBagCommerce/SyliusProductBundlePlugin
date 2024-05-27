<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Tests\BitBag\SyliusProductBundlePlugin\Entity\Product;

final class AddProductToProductBundleWhenEditNormalProductEventListener
{
    public function addProductToProductBundle(ResourceControllerEvent $resourceControllerEvent): void
    {
        /** @var Product $product */
        $product = $resourceControllerEvent->getSubject();
        if (null !== $product->getProductBundle() && null === $product->getProductBundle()->getProduct()) {
            $product->getProductBundle()->setProduct($product);
        }
    }
}

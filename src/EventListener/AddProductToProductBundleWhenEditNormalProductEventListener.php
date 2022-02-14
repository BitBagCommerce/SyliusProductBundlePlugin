<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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

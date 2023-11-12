<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusProductBundlePlugin\EventListener;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductInterface;
use BitBag\SyliusProductBundlePlugin\EventListener\AddProductToProductBundleWhenEditNormalProductEventListener;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

final class AddProductToProductBundleWhenEditNormalProductEventListenerSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductToProductBundleWhenEditNormalProductEventListener::class);
    }

    public function it_should_add_product_to_product_bundle_if_not_exist_on_pre_create_and_update_event(
        ResourceControllerEvent $resourceControllerEvent,
        ProductInterface $product,
        ProductBundleInterface $productBundle
    ): void {
        $resourceControllerEvent->getSubject()->willReturn($product);

        $product->getProductBundle()->shouldBeCalled();

        $product->getProductBundle()->willReturn($productBundle);

        $this->addProductToProductBundle($resourceControllerEvent);
    }
}

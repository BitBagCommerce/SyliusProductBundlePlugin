<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\EventListener;

use BitBag\SyliusProductBundlePlugin\Dto\AddProductBundleToCartDtoInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class CartItemAddListener
{
    public function __construct(
        private readonly OrderModifierInterface  $orderModifier
    ) {
    }

    public function addToOrder(GenericEvent $event): void
    {
        $addToCartCommand = $event->getSubject();

        Assert::isInstanceOf($addToCartCommand, AddProductBundleToCartDtoInterface::class);

        $this->orderModifier->addToOrder($addToCartCommand->getCart(), $addToCartCommand->getCartItem());
    }
}

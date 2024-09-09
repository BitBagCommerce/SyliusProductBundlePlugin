<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Handler\AddProductBundleToCartHandler;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Order\Model\OrderInterface;

interface CartProcessorInterface
{
    public function process(
        OrderInterface $cart,
        ProductBundleInterface $productBundle,
        int $quantity,
        Collection $productBundleOrderItems,
    ): void;
}

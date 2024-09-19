<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Inventory\Operator;

use Sylius\Component\Core\Model\OrderInterface;

interface ProductBundleOrderInventoryOperatorInterface
{
    public function hold(OrderInterface $order): void;

    public function sell(OrderInterface $order): void;

    /** @throws \InvalidArgumentException */
    public function release(OrderInterface $order): void;

    public function giveBack(OrderInterface $order): void;
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderMother
{
    public static function create(): OrderInterface
    {
        return new Order();
    }

    public static function createWithId(int $id): OrderInterface
    {
        $order = self::create();

        $setIdClosure = function (int $id): void {
            /** @phpstan-ignore-next-line  */
            $this->id = $id;
        };
        ($setIdClosure->bindTo($order, $order))($id);

        return $order;
    }

    public static function createWithChannel(ChannelInterface $channel): OrderInterface
    {
        $order = self::create();

        $order->setChannel($channel);

        return $order;
    }
}

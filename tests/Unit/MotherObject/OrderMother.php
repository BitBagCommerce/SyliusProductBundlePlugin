<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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

        $orderReflection = new \ReflectionClass($order);
        $idProperty = $orderReflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($order, $id);

        return $order;
    }

    public static function createWithChannel(ChannelInterface $channel): OrderInterface
    {
        $order = self::create();

        $order->setChannel($channel);

        return $order;
    }
}

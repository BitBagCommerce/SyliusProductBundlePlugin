<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject;

use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelMother
{
    public static function create(): ChannelInterface
    {
        return new Channel();
    }

    public static function createWithName(string $name): ChannelInterface
    {
        $channel = self::create();

        $channel->setName($name);

        return $channel;
    }
}

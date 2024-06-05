<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Api\Utils;

use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

trait CartHelperTrait
{
    public function createCart(string $tokenValue): void
    {
        /** @var MessageBusInterface $commandBus */
        $commandBus = self::getContainer()->get('sylius.command_bus');

        $command = new PickupCart($tokenValue, 'en_US');
        $command->setChannelCode('WEB');

        $commandBus->dispatch($command);
    }

    public function findCart(string $tokenValue): ?OrderInterface
    {
        /** @var OrderRepositoryInterface $orderManager */
        $orderManager = self::getContainer()->get('sylius.repository.order');

        return $orderManager->findCartByTokenValue($tokenValue);
    }
}

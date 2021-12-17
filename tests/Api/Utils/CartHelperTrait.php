<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
        $commandBus = $this->getContainer()->get('sylius.command_bus');

        $command = new PickupCart($tokenValue, 'en_US');
        $command->setChannelCode('WEB');

        $commandBus->dispatch($command);
    }

    public function findCart(string $tokenValue): ?OrderInterface
    {
        /** @var OrderRepositoryInterface $orderManager */
        $orderManager = $this->getContainer()->get('sylius.repository.order');

        return $orderManager->findCartByTokenValue($tokenValue);
    }
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Factory;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommandInterface;
use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleToCartCommand;
use BitBag\SyliusProductBundlePlugin\Factory\AddProductBundleToCartCommandFactory;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\AddProductBundleToCartDtoMother;

final class AddProductBundleToCartCommandFactoryTest extends TestCase
{
    public const ORDER_ID = 5;

    public const PRODUCT_CODE = 'MY_PRODUCT';

    public const QUANTITY = 2;

    public function testCreateAddProductBundleToCartCommandObject(): void
    {
        $addProductBundleItemToCartCommand = $this->createMock(AddProductBundleItemToCartCommandInterface::class);
        $commands = new ArrayCollection([$addProductBundleItemToCartCommand]);

        $factory = new AddProductBundleToCartCommandFactory();
        $command = $factory->createNew(self::ORDER_ID, self::PRODUCT_CODE, self::QUANTITY, $commands);

        self::assertInstanceOf(AddProductBundleToCartCommand::class, $command);
        self::assertEquals(self::ORDER_ID, $command->getOrderId());
        self::assertEquals(self::PRODUCT_CODE, $command->getProductCode());
        self::assertEquals(self::QUANTITY, $command->getQuantity());
        self::assertEquals($commands, $command->getProductBundleItems());
    }

    public function testCreateAddProductBundleToCartCommandObjectFromDto(): void
    {
        $dto = AddProductBundleToCartDtoMother::createWithOrderIdAndProductCode(self::ORDER_ID, self::PRODUCT_CODE);

        $factory = new AddProductBundleToCartCommandFactory();
        $command = $factory->createFromDto($dto);

        self::assertInstanceOf(AddProductBundleToCartCommand::class, $command);
        self::assertEquals(self::ORDER_ID, $command->getOrderId());
        self::assertEquals(self::PRODUCT_CODE, $command->getProductCode());
        self::assertEquals(0, $command->getQuantity());
    }
}

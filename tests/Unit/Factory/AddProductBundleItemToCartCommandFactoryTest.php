<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Factory;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommand;
use BitBag\SyliusProductBundlePlugin\Factory\AddProductBundleItemToCartCommandFactory;
use PHPUnit\Framework\TestCase;
use Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\ProductBundleItemMother;

final class AddProductBundleItemToCartCommandFactoryTest extends TestCase
{
    public function testCreateAddProductBundleItemToCartCommand(): void
    {
        $productBundleItem = ProductBundleItemMother::create();

        $factory = new AddProductBundleItemToCartCommandFactory();
        $command = $factory->createNew($productBundleItem);

        self::assertInstanceOf(AddProductBundleItemToCartCommand::class, $command);
    }
}

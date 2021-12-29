<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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

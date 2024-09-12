<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\Inventory\Checker;

use BitBag\SyliusProductBundlePlugin\Inventory\Checker\BundledProductsInventoryManagementFeatureFlagChecker;
use PHPUnit\Framework\TestCase;

final class BundledProductsInventoryManagementFeatureFlagCheckerTest extends TestCase
{
    public function testIsEnabled(): void
    {
        $checker = new BundledProductsInventoryManagementFeatureFlagChecker(true);
        self::assertTrue($checker->isEnabled());
    }

    public function testIsDisabled(): void
    {
        $checker = new BundledProductsInventoryManagementFeatureFlagChecker(false);
        self::assertFalse($checker->isEnabled());
    }
}

<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusProductBundlePlugin\Unit\MotherObject\Api;

use BitBag\SyliusProductBundlePlugin\Dto\Api\AddProductBundleToCartDto;

final class AddProductBundleToCartDtoMother
{
    public static function create(string $productCode, int $quantity = 1): AddProductBundleToCartDto
    {
        return new AddProductBundleToCartDto($productCode, $quantity);
    }
}

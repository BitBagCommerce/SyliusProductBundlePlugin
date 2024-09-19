<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Sylius\SyliusRector\Rector\Class_\AddInterfaceToClassExtendingTypeRector;
use Sylius\SyliusRector\Rector\Class_\AddTraitToClassExtendingTypeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(AddInterfaceToClassExtendingTypeRector::class, [
        'Sylius\Component\Core\Model\OrderItem' => [
            'BitBag\SyliusProductBundlePlugin\Entity\OrderItemInterface',
        ],
        'Sylius\Component\Core\Model\Product' => [
            'BitBag\SyliusProductBundlePlugin\Entity\ProductInterface',
        ],
    ]);
    $rectorConfig->ruleWithConfiguration(AddTraitToClassExtendingTypeRector::class, [
        'Sylius\Component\Core\Model\OrderItem' => [
            'BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemsAwareTrait',
        ],
        'Sylius\Component\Core\Model\Product' => [
            'BitBag\SyliusProductBundlePlugin\Entity\ProductBundlesAwareTrait',
        ],
    ]);
};

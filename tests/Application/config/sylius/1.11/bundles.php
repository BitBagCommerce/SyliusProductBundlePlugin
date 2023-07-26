<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

$bundles = [
    Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle::class => ['all' => true],
];

if (class_exists('BabDev\PagerfantaBundle\BabDevPagerfantaBundle')) {
    $bundles[BabDev\PagerfantaBundle\BabDevPagerfantaBundle::class] = ['all' => true];
}
if (class_exists('SyliusLabs\Polyfill\Symfony\Security\Bundle\SyliusLabsPolyfillSymfonySecurityBundle')) {
    $bundles[SyliusLabs\Polyfill\Symfony\Security\Bundle\SyliusLabsPolyfillSymfonySecurityBundle::class] = ['all' => true];
}
if (class_exists('Sylius\Calendar\SyliusCalendarBundle')) {
    $bundles[Sylius\Calendar\SyliusCalendarBundle::class] = ['all' => true];
}

return $bundles;

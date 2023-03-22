<?php

declare(strict_types=1);

$bundles = [
    Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle::class => ['all' => true],
    Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],
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

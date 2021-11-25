<?php

declare(strict_types=1);

$bundles = [];

if (class_exists('BabDev\PagerfantaBundle\BabDevPagerfantaBundle')) {
    $bundles[BabDev\PagerfantaBundle\BabDevPagerfantaBundle::class] = ['all' => true];
}
if (class_exists('SyliusLabs\Polyfill\Symfony\Security\Bundle\SyliusLabsPolyfillSymfonySecurityBundle')) {
    $bundles[SyliusLabs\Polyfill\Symfony\Security\Bundle\SyliusLabsPolyfillSymfonySecurityBundle::class] = ['all' => true];
}

return $bundles;

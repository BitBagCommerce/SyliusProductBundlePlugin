<?php

declare(strict_types=1);

$bundles = [];

if (class_exists('BabDev\PagerfantaBundle\BabDevPagerfantaBundle')) {
    $bundles[BabDev\PagerfantaBundle\BabDevPagerfantaBundle::class] = ['all' => true];
}
if (class_exists('SyliusLabs\Polyfill\Symfony\Security\Bundle\SyliusLabsPolyfillSymfonySecurityBundle')) {
    $bundles[SyliusLabs\Polyfill\Symfony\Security\Bundle\SyliusLabsPolyfillSymfonySecurityBundle::class] = ['all' => true];
}
if (class_exists('FOS\OAuthServerBundle\FOSOAuthServerBundle')) {
    $bundles[FOS\OAuthServerBundle\FOSOAuthServerBundle::class] = ['all' => true];
}
if (class_exists('Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle')) {
    $bundles[Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle::class] = ['all' => true];
}

return $bundles;

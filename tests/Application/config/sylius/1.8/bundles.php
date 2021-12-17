<?php

declare(strict_types=1);

$bundles = [];

if (class_exists('Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle')) {
    $bundles[Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle::class] = ['all' => true];
}
if (class_exists('WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle')) {
    $bundles[WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle::class] = ['all' => true];
}
if (class_exists('FOS\OAuthServerBundle\FOSOAuthServerBundle')) {
    $bundles[FOS\OAuthServerBundle\FOSOAuthServerBundle::class] = ['all' => true];
}
if (class_exists('Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle')) {
    $bundles[Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle::class] = ['all' => true];
}

return $bundles;

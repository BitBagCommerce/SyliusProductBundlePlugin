<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AuthenticationManagerPolyfillPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (
            false === $container->has('security.authentication_manager')
            &&
            true === $container->has('security.authentication.manager')
        ) {
            $container->setAlias('security.authentication_manager', 'security.authentication.manager');
        }
    }
}

<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\DependencyInjection;

use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class BitBagSyliusProductBundleExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    public function load(array $configs, ContainerBuilder $container): void
    {
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDoctrineMigrations($container);

        $config = $this->getCurrentConfiguration($container);
        $this->registerResources('bitbag_sylius_product_bundle', 'doctrine/orm', $config['resources'], $container);
    }

    protected function getMigrationsNamespace(): string
    {
        return 'BitBag\SyliusProductBundlePlugin\Migrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@BitBagSyliusProductBundlePlugin/Migrations';
    }

    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return ['Sylius\Bundle\CoreBundle\Migrations'];
    }

    private function getCurrentConfiguration(ContainerBuilder $container): array
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->getConfiguration([], $container);

        $configs = $container->getExtensionConfig($this->getAlias());

        return $this->processConfiguration($configuration, $configs);
    }
}

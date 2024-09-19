<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\DependencyInjection;

use BitBag\SyliusProductBundlePlugin\Entity\ProductBundle;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItem;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleItemInterface;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItem;
use BitBag\SyliusProductBundlePlugin\Entity\ProductBundleOrderItemInterface;
use BitBag\SyliusProductBundlePlugin\Form\Type\ProductBundleItemType;
use BitBag\SyliusProductBundlePlugin\Form\Type\ProductBundleType;
use BitBag\SyliusProductBundlePlugin\Repository\ProductBundleItemRepository;
use BitBag\SyliusProductBundlePlugin\Repository\ProductBundleRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('bitbag_sylius_product_bundle');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('product_bundle')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ProductBundle::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProductBundleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ProductBundleRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(ProductBundleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('product_bundle_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ProductBundleItem::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProductBundleItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ProductBundleItemRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(ProductBundleItemType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('product_bundle_order_item')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ProductBundleOrderItem::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProductBundleOrderItemInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
